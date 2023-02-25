<?php

namespace CraftyDigit\Puff;

use CraftyDigit\Puff\ErrorReporter\ErrorReporter;
use CraftyDigit\Puff\ErrorReporter\ErrorReporterInterface;
use CraftyDigit\Puff\Router\Router;
use CraftyDigit\Puff\Router\RouterInterface;
use Exception;

final class Kernel
{
    /**
     * @param RouterInterface $router
     * @param ErrorReporterInterface $errorReporter
     */
    public function __construct(
        public ErrorReporterInterface $errorReporter = new ErrorReporter(),
        public RouterInterface $router = new Router()
    )
    {}

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