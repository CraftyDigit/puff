<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Attributes\Controller;
use CraftyDigit\Puff\Attributes\Singleton;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use ReflectionClass;
use CraftyDigit\Puff\Exceptions\ClassNotFoundException;
use CraftyDigit\Puff\Helper;

#[Singleton]
class ControllerManager implements ControllerManagerInterface
{
    public function __construct(
        private readonly ContainerExtendedInterface $container,
        private readonly Helper $helper,
        private array $controllersClasses = [],
    )
    {
        $this->setControllersClasses();
    }

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

    public function getControllersClasses(): array
    {
        return $this->controllersClasses;
    }

    public function getController(string $name): AbstractController
    {
        if (!isset($this->controllersClasses[$name])) {
            throw new ClassNotFoundException("Controller class '$name' not found");
        }
        
        $class = $this->controllersClasses[$name];

        return $this->container->get($class);
    }
}