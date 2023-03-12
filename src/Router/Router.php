<?php

namespace CraftyDigit\Puff\Router;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Controller\ControllerManager;
use CraftyDigit\Puff\Controller\ControllerManagerInterface;
use CraftyDigit\Puff\Attributes\Route;
use CraftyDigit\Puff\Enums\AppMode;
use CraftyDigit\Puff\Enums\RequestMethod;
use CraftyDigit\Puff\Exceptions\RouteNotFoundException;
use CraftyDigit\Puff\Helper;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Exception;

class Router implements RouterInterface
{
    /**
     * @var Router|null 
     */
    private static ?Router $instance = null;

    /**
     * @param array $routes
     * @param Config|null $config
     * @param ControllerManagerInterface $controllerManager
     * @param Helper $helper
     * @throws Exception
     */
    private function __construct(
        public array $routes = [],
        protected ?Config $config = null,
        protected readonly ControllerManagerInterface $controllerManager = new ControllerManager(),
        protected readonly Helper $helper = new Helper()
    )
    {
        $this->config = Config::getInstance();
        $this->registerControllersRoutes();
    }

    /**
     * @return Router
     */
    public static function getInstance(): Router
    {
        if (self::$instance == null) {
            self::$instance = new Router();
        }

        return self::$instance;
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    protected function registerControllersRoutes(): void
    {
        $controllers = $this->controllerManager->getControllersClasses();
        
        foreach ($controllers as $name => $class) {
            $this->registerControllerRoutes($class);
        }
    }

    /**
     * @param string $controllerClass
     * @return void
     * @throws ReflectionException
     */
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

    /**
     * @param array $requestParams
     * @return void
     * @throws Exception
     */
    public function followRoute(array $requestParams = []): void
    {
        $path = explode('?', $_SERVER['REQUEST_URI'])[0];
        
        $requestMethod = RequestMethod::from($_SERVER['REQUEST_METHOD']);

        $this->addParamsToGlobalRequestParams($requestParams, $requestMethod);
        
        if (
            isset($this->routes[$requestMethod->value][$path]) 
            && $this->routes[$requestMethod->value][$path]['isPublic']
        ) {
            $controller = $this->routes[$requestMethod->value][$path]['controller'];
            $method = $this->routes[$requestMethod->value][$path]['method'];
            
            $controller = new $controller();
            
            $controller->$method();
        } else {
            if (AppMode::from($this->config->mode) === AppMode::DEV) {
                throw new RouteNotFoundException(route: $path);
            } else {
                $this->followRouteByName('error_404');
            }
        }
    }

    /**
     * @param string $name
     * @param array $requestParams
     * @return void
     * @throws Exception
     */
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
        
        $controller = new $controller();
        
        $controller->$method();
    }

    /**
     * @param string $name
     * @return array|null
     */
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

    /**
     * @param string $path
     * @param array $requestParams
     * @param RequestMethod $method
     * @return void
     * @throws Exception
     */
    public function redirect(string $path, array $requestParams = [], RequestMethod $method = RequestMethod::GET): void
    {
        if ($method === RequestMethod::GET) {
            if (!empty($requestParams)) {
                $path .= '?' . http_build_query($requestParams);
            }
            header('Location: ' . $path);
            exit;
        }

        if ($method === RequestMethod::POST) {
            $formId = uniqid('form_');

            $html = '<form style="display: none;" id="' . $formId . '" action="' . $path . '" method="post">';

            foreach ($requestParams as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $html .= '<input type="hidden" name="' . $key . '[' . $subKey . ']" value="' . $subValue . '">';
                    }
                } else {
                    $html .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
                }
            }

            $html .= '<input type="submit" value="Redirect">';
            $html .= '</form>';
            $html .= '<script> document.getElementById("' . $formId . '").submit() </script>';
            
            echo $html;
            
            exit;
        }

        throw new Exception('Invalid redirect method specified.');
    }

    /**
     * @param string $name
     * @param array|null $requestParams
     * @return void
     * @throws RouteNotFoundException
     */
    public function redirectToRouteByName(string $name, array $requestParams = []): void
    {
        $route = $this->getRouteByName($name);

        if (!$route) {
            if (AppMode::from($this->config->mode) === AppMode::DEV) {
                throw new RouteNotFoundException('Route with name "' . $name . '" not found');
            } else {
                $route = $this->getRouteByName('error_404');
                $requestParams = [];
            }
        }

        $redirectPath = $route['path'];
        
        $this->redirect($redirectPath, $requestParams, $route['requestMethod']);
    }

    /**
     * @param array $params
     * @param RequestMethod $requestMethod
     * @return void
     * @throws Exception
     */
    private function addParamsToGlobalRequestParams(
        array $params, RequestMethod $requestMethod = RequestMethod::GET): void
    {
        if ($params) {
            if ($requestMethod === RequestMethod::GET) {
                $_GET += $params;
            } else if ($requestMethod === RequestMethod::POST) {
                $_POST += $params;
            } else {
                throw new Exception('Request method "' . $requestMethod . '" not supported');
            }
        }
    }
}