<?php

namespace CraftyDigit\Puff\Router;

use CraftyDigit\Puff\Attributes\Singleton;
use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Controller\ControllerManagerInterface;
use CraftyDigit\Puff\Attributes\Route;
use CraftyDigit\Puff\Enums\AppMode;
use CraftyDigit\Puff\Enums\RequestMethod;
use CraftyDigit\Puff\Exceptions\RequestMethodNotSupportedException;
use CraftyDigit\Puff\Exceptions\RouteNotFoundException;
use CraftyDigit\Puff\Http\HttpManagerInterface;
use ReflectionClass;
use ReflectionMethod;

#[Singleton]
class Router implements RouterInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly ControllerManagerInterface $controllerManager,
        private readonly ContainerExtendedInterface $container,
        private readonly HttpManagerInterface $httpManager,
        public array $routes = [],
    )
    {}
    
    public function registerRoutes(): void
    {
        $this->registerControllersRoutes();
    }

    protected function registerControllersRoutes(): void
    {
        $controllers = $this->controllerManager->getControllersClasses();
        
        foreach ($controllers as $name => $class) {
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
        ?string $url = null, 
        array $requestParams = [], 
        ?RequestMethod $requestMethod = null): void
    {
        $serverRequest = $this->httpManager->getServerRequest();
        
        $url = $url ?? $serverRequest->getUri()->getPath();
        
        $requestMethod = $requestMethod ?? RequestMethod::from($serverRequest->getMethod());

        $this->addParamsToGlobalRequestParams($requestParams, $requestMethod);
        
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
            $controller->$method(...$params);
        } else {
            $this->handleRouteNotFound($url, $requestMethod);
        }
    }

    public function followRouteByName(string $name, array $requestParams = []): void
    {
        $route = $this->getRouteByName($name);

        if (!$route) {
            if (AppMode::from($this->config->mode) === AppMode::DEV) {
                throw new RouteNotFoundException('Route with name "' . $name . '" not found');
            } else {
                $route = $this->getRouteByName('error_404');
            }
        }

        $requestMethod = $route['requestMethod'];

        $this->addParamsToGlobalRequestParams($requestParams, $requestMethod);

        $controller = $route['controller'];
        $method = $route['method'];
        
        $controller = $this->container->get($controller); 
        
        $controller->$method();
    }

    protected function getRouteByName(string $name): ?array
    {
        foreach ($this->routes as $method => $routes) {
            foreach ($routes as $path => $route) {
                if ($route['name'] === $name) {
                    return $route;
                }
            }
        }
        
        return null;
    }

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

    private function handleRouteNotFound(string $url, RequestMethod $requestMethod)
    {
        if (AppMode::from($this->config->mode) === AppMode::DEV) {
            throw new RouteNotFoundException(route: $url, requestMethod: $requestMethod);
        } else {
            $this->followRouteByName('error_404');
        }
    }
}