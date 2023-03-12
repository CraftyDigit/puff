<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Attributes\Controller;
use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\ErrorReporter\ErrorCode;
use ReflectionClass;
use ReflectionException;
use CraftyDigit\Puff\Exceptions\ClassNotFoundException;
use CraftyDigit\Puff\Helper;
use Exception;

class ControllerManager implements ControllerManagerInterface
{
    /**
     * @param Config|null $config
     */
    public function __construct(
        protected array $controllersClasses = [],
        protected ?Config $config = null,
        protected readonly Helper $helper = new Helper()
    )
    {
        $this->config = Config::getInstance();
        $this->setControllersClasses();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function setControllersClasses(): void
    {
        $classes = [];

        $filesNames = $this->helper->getAppDirectoryFiles('Controllers');

        foreach ($filesNames as $fileName) {
            $controllerClass = str_replace('.php', '', $fileName);
            $controllerClass = str_replace(DIRECTORY_SEPARATOR, '\\', $controllerClass);
            $controllerClass = 'App' . $controllerClass;

            $reflectionClass = new ReflectionClass($controllerClass);

            $attributes = $reflectionClass->getAttributes(Controller::class);
            
            if (empty($attributes)) {
                continue;
            }

            $classes[$attributes[0]->newInstance()->name] = $controllerClass;
        }

        $this->controllersClasses = $classes;
    }

    /**
     * @return array
     */
    public function getControllersClasses(): array
    {
        return $this->controllersClasses;
    }

    /**
     * @param string $name
     * @return ControllerInterface
     * @throws ClassNotFoundException
     */
    public function getController(string $name): ControllerInterface
    {
        if (!isset($this->controllersClasses[$name])) {
            throw new ClassNotFoundException("Controller class '$name' not found");
        }
        
        $class = $this->controllersClasses[$name];

        return new $class();
    }
}