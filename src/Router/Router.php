<?php

namespace CraftyDigit\Puff\Router;

use CraftyDigit\Puff\Attributes\Route;
use CraftyDigit\Puff\Attributes\Singleton;
use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Controller\ControllerManagerInterface;
use CraftyDigit\Puff\Enums\AppMode;
use CraftyDigit\Puff\Enums\RequestMethod;
use CraftyDigit\Puff\Exceptions\ControllerException;
use CraftyDigit\Puff\Exceptions\RouteNotFoundException;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use ReflectionMethod;

#[Singleton]
class Router implements RouterInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly ControllerManagerInterface $controllerManager,
        private readonly ContainerExtendedInterface $container,
        public array $routes = [],
    )
    {}
    
    public function registerRoutes(): void
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
        array $requestParams = [], 
        RequestMethod $requestMethod = RequestMethod::GET): ResponseInterface
    {
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

                $controller = $this->container->get($route['controller']);
                $method = $route['method'];
                
                break;
            }
            
        }

        if ($controller && $method && method_exists($controller, $method)) {
            return $controller->$method(...$params);
        } else {
            return $this->followRouteNotFound($url, $requestMethod);
        }
    }

    public function followRouteByName(string $name, array $requestParams = []): ResponseInterface
    {
        $route = $this->getRouteByName($name);

        if (!$route) {
            return $this->followRouteNotFound($name, RequestMethod::GET, true);
        }

        $controller = $route['controller'];
        $method = $route['method'];
        
        $controller = $this->container->get($controller);

        return $controller->$method();
    }

    protected function getRouteByName(string $name): ?array
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

    /* Not need?
     * 
     * 
    private function addParamsToGlobalRequestParams(
        array $params, RequestMethod $requestMethod = RequestMethod::GET): void
    {
        $serverRequest = $this->httpManager->getServerRequest();
        
        if ($params) {
            if ($requestMethod === RequestMethod::GET) {
                $serverRequest->withQueryParams($params);
            } else if ($requestMethod === RequestMethod::POST) {
                $serverRequest->withParsedBody($params);
            } else {
                throw new RequestMethodNotSupportedException('Request method "' . $requestMethod . '" is not supported');
            }
        }
    }
    */

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

    private function followRouteNotFound(string $route, RequestMethod $requestMethod, bool $routeIsName = false): ResponseInterface
    {
        if (AppMode::from($this->config->mode) === AppMode::DEV) {
            if ($routeIsName) {
                throw new RouteNotFoundException('Route with name "' . $route . '" not found');
            } else {
                throw new RouteNotFoundException(route: $route, requestMethod: $requestMethod);
            }
        } else {
            return $this->followRouteByName('error_404');
        }
    }
}