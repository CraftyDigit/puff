<?php

namespace CraftyDigit\Puff;

use CraftyDigit\Puff\ErrorReporter\ErrorReporterInterface;
use CraftyDigit\Puff\Http\HttpManager;

final readonly class Kernel
{
    public function __construct(
        private HttpManager $httpManager,
        private ErrorReporterInterface $errorReporter
    )
    {}

    /**
     * This method starts the app
     */
    function start(): void
    {
        $this->errorReporter->registerHandlers();
        $this->httpManager->processRequest();
    }
}