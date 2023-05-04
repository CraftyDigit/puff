<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Router\RouterInterface;

abstract class AbstractController
{
    public function __construct(
        protected ContainerExtendedInterface $container,
        protected readonly RouterInterface $router,
        protected readonly ControllerManagerInterface $controllerManager,
        protected readonly Config $config,
    )
    {}
}