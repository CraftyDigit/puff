<?php

namespace CraftyDigit\Puff\ErrorReporter;

interface ErrorReporterInterface
{
    /**
     * This method enables correct error handling and reporting
     */
    public function setHandlers(): void;

    public function exceptionHandler($e): void;

    public function errorHandler($level, $message, string $file = '', int $line = 0): void;

    public function criticalErrorHandler(): void;
}