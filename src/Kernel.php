<?php

namespace CraftyDigit\Puff;

use CraftyDigit\Puff\Controller\ControllerManager;
use CraftyDigit\Puff\ErrorReporter\ErrorReporter;
use CraftyDigit\Puff\ErrorReporter\ErrorReporterInterface;
use CraftyDigit\Puff\Router\Router;
use CraftyDigit\Puff\Router\RouterInterface;
use Exception;

final class Kernel
{
    /**
     * @param RouterInterface|null $router
     * @param ErrorReporterInterface $errorReporter
     */
    public function __construct(
        private ?RouterInterface $router = null,
        private readonly ErrorReporterInterface $errorReporter = new ErrorReporter()
    )
    {
        $this->router = $this->router ?? Router::getInstance();
    }

    /**
     * This method will start the app
     *
     * @return void
     * @throws Exception
     */
    function start(): void
    {
        $this->errorReporter->setHandlers();
        $this->router->followRoute();
    }
}