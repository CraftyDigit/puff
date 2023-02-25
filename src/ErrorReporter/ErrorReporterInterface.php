<?php

namespace CraftyDigit\Puff\ErrorReporter;

interface ErrorReporterInterface
{
    /**
     * This method enables correct error handling and reporting
     *
     * @return void
     */
    public function setHandlers(): void;

    /**
     * @param $e
     * @return void
     */
    public function exceptionHandler($e): void;

    /**
     * @param $level
     * @param $message
     * @param string $file
     * @param int $line
     * @return void
     */
    public function errorHandler($level, $message, string $file = '', int $line = 0): void;

    /**
     * @return void
     */
    public function criticalErrorHandler(): void;
}