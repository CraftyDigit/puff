<?php

namespace CraftyDigit\Puff;

use CraftyDigit\Puff\ErrorReporter\ErrorReporterInterface;
use CraftyDigit\Puff\Router\RouterInterface;
use Exception;

final readonly class Kernel
{
    /**
     * @param RouterInterface $router
     * @param ErrorReporterInterface $errorReporter
     */
    public function __construct(
        private RouterInterface $router,
        private ErrorReporterInterface $errorReporter
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