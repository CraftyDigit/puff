<?php

namespace CraftyDigit\Puff;

use CraftyDigit\Puff\ErrorReporter\ErrorReporterInterface;
use CraftyDigit\Puff\Router\RouterInterface;

final readonly class Kernel
{
    public function __construct(
        private RouterInterface $router,
        private ErrorReporterInterface $errorReporter
    )
    {}

    /**
     * This method will start the app
     */
    function start(): void
    {
        $this->errorReporter->setHandlers();
        $this->router->followRoute();
    }
}