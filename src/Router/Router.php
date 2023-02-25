<?php

namespace CraftyDigit\Puff\Router;

use CraftyDigit\Puff\Controller\ControllerManager;
use CraftyDigit\Puff\Controller\ControllerManagerInterface;
use Exception;

class Router implements RouterInterface
{
    /**
     * @param ControllerManagerInterface $controllerManager
     */
    public function __construct(
        public ControllerManagerInterface $controllerManager = new ControllerManager()
    )
    {}

    /**
     * @return void
     * @throws Exception
     */
    function followRoute(): void
    {
        $path = explode('?', $_SERVER['REQUEST_URI'])[0];

        if ($path === '/') {
            $controllerName = 'Homepage';
            $relatedPath = '/';
        } else {
            $pathArr = explode('/', $path);

            $controllerName = ucfirst($pathArr[sizeof($pathArr)-1]);

            $relatedPath = '';

            for ($i = 1; $i < sizeof($pathArr)-1; $i++) {
                $relatedPath .= '/' . ucfirst($pathArr[$i]);
            }
        }

        if (str_contains($relatedPath, '/Admin')) {
            $isAdminController = true;
            $relatedPath = str_replace('/Admin', '', $relatedPath);
        } else {
            $isAdminController = false;
        }

        $relatedPath = $relatedPath ?: '/';

        $controller = $this->controllerManager->getController($controllerName, $isAdminController, $relatedPath);

        $controller->render();
    }
}