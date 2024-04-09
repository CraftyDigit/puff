<?php

namespace CraftyDigit\Puff\Router;

use CraftyDigit\Puff\Attributes\Route;
use CraftyDigit\Puff\Attributes\Singleton;
use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Controller\ControllerManagerInterface;
use CraftyDigit\Puff\Common\Enums\AppMode;
use CraftyDigit\Puff\Common\Enums\RequestMethod;
use CraftyDigit\Puff\Exceptions\ControllerException;
use CraftyDigit\Puff\Exceptions\RouteNotFoundException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use ReflectionMethod;

#[Singleton]
class RouteManager implements RouteManagerInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly ControllerManagerInterface $controllerManager,
        private readonly ContainerExtendedInterface $container,
        public array $routes = [],
    )
    {
        $this->registerResources();
    }
    
    public function registerResources(): void
    {
        $this->registerControllersRoutes();
    }

    protected function registerControllersRoutes(): void
    {
        // TODO: can use cache to store the routes, the same apply to static lists in other classes (like eventListeners, etc.)
        $controllersClasses = $this->controllerManager->getControllersClasses();
        
        foreach ($controllersClasses as $class) {
            $this->registerControllerRoutes($class);
        }
    }

    protected function registerControllerRoutes(string $controllerClass): void
    {
        $reflectionClass = new ReflectionClass($controllerClass);
        
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $attributes = $method->getAttributes(Route::class);
            
            foreach ($attributes as $attribute) {
                if ($method->getReturnType()->getName() !== ResponseInterface::class) {
                    throw new ControllerException("Method '$method->name' in controller '$controllerClass' should return ResponseInterface");
                }

                $routeAttribute = $attribute->newInstance();
                
                $this->routes[$routeAttribute->requestMethod->name][$routeAttribute->path] = [
                    'path' => $routeAttribute->path,
                    'name' => $routeAttribute->name,
                    'requestMethod' => $routeAttribute->requestMethod,
                    'controller' => $controllerClass,
                    'method' => $method->getName(),
                    'isPublic' => $routeAttribute->isPublic
                ];
            }
        }
    }

    public function followRoute(
        string $url,
        ?RequestInterface $request = null
    ): ResponseInterface
    {
        $requestMethod = RequestMethod::tryFrom($request->getMethod());
        $controller = null;
        $method = null;
        $params = [];

        foreach ($this->routes[$requestMethod->value] as $route) {
            
            if (!$route['isPublic']) {
                continue;
            }
            
            $pattern = $this->convertPathToRegex($route['path']);
            
            if (preg_match('#^' . $pattern . '$#', $url, $matches)) {
                array_shift($matches); // remove the first element, which is the full match
                
                $params = $this->getParamsFromMatches($matches, $route['path']);

                $controller = $this->container->get($route['controller'], ['request' => $request]);
                $method = $route['method'];
                
                break;
            }
            
        }

        if ($controller && $method && method_exists($controller, $method)) {
            return $controller->$method(...$params);
        } else {
            return $this->routeNotFound($url, $request);
        }
    }

    public function followRouteByName(
        string $name,
        ?RequestInterface $request = null
    ): ResponseInterface
    {
        $route = $this->getRouteByName($name);

        if (!$route) {
            return $this->routeNotFound($name, $request, true);
        }

        $controller = $route['controller'];
        $method = $route['method'];
        
        $controller = $this->container->get($controller, ['request' => $request]);

        return $controller->$method();
    }

    public function getRouteByName(string $name): ?array
    {
        foreach ($this->routes as $methodRoutes) {
            foreach ($methodRoutes as $route) {
                if ($route['name'] === $name) {
                    return $route;
                }
            }
        }
        
        return null;
    }

    private function convertPathToRegex(string $url): string 
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^\/]+)', $url); // replace {parameter} with (?P<parameter>[^\/]+)
        
        return str_replace('/', '\/', $pattern); // escape slashes in the pattern
    }

    private function getParamsFromMatches(array $matches, string $path): array 
    {
        $params = [];
        
        preg_match_all('/\{(\w+)\}/', $path, $paramNames); // extract parameter names from the URL
        
        foreach ($paramNames[1] as $name) {
            $params[] = $matches[$name];
        }
        
        return $params;
    }

    private function routeNotFound(
        string $route,
        ?RequestInterface $request = null,
        bool $routeIsName = false
    ): ResponseInterface
    {
        if (AppMode::from($this->config->mode) === AppMode::DEV) {
            $requestMethod = RequestMethod::tryFrom($request->getMethod());

            if ($routeIsName) {
                throw new RouteNotFoundException('Route with name "' . $route . '" not found');
            } else {
                throw new RouteNotFoundException(route: $route, requestMethod: $requestMethod);
            }
        } else {
            return $this->followRouteByName('error_404', $request);
        }
    }
}