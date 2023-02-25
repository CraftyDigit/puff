<?php

namespace CraftyDigit\Puff\ErrorReporter;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Controller\ControllerManager;
use CraftyDigit\Puff\Controller\ControllerManagerInterface;
use CraftyDigit\Puff\Exceptions\ClassNotFoundException;
use ErrorException;

class ErrorReporter implements ErrorReporterInterface
{
    /**
     * @var Config
     */
    public Config $config;

    /**
     * @param ControllerManagerInterface $controllerManager
     */
    public function __construct(public ControllerManagerInterface $controllerManager = new ControllerManager())
    {
        $this->config = Config::getInstance();
    }

    /**
     * This method enables correct error handling and reporting
     *
     * @return void
     */
    public function setHandlers(): void
    {
        error_reporting(E_ALL);

        if ($this->config->mode === 'prod') {
            ini_set('display_errors', false);
            ini_set('log_errors', true);
        } else {
            ini_set('display_errors', true);
            ini_set('log_errors', false);
        }

        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler']);
        register_shutdown_function([$this, 'criticalErrorHandler']);
    }

    /**
     * @param $e
     * @return void
     * @throws ClassNotFoundException
     */
    public function exceptionHandler($e): void
    {
        error_log($e);
        http_response_code(500);

        if (filter_var(ini_get('display_errors'), FILTER_VALIDATE_BOOLEAN)) {
            echo $e;
        } else {
            $this->controllerManager->getErrorController(ErrorCode::Error500)->render();
        }

        exit;
    }

    /**
     * @param $level
     * @param $message
     * @param string $file
     * @param int $line
     * @return void
     * @throws ClassNotFoundException
     */
    public function errorHandler($level, $message, string $file = '', int $line = 0): void
    {
        $e = new ErrorException($message, 0, $level, $file, $line);
        $this->exceptionHandler($e);
    }

    /**
     * @return void
     * @throws ClassNotFoundException
     */
    public function criticalErrorHandler(): void
    {
        $error = error_get_last();
        if ($error !== null) {
            $e = new ErrorException(
                $error['message'], 0, $error['type'], $error['file'], $error['line']
            );
            $this->exceptionHandler($e);
        }
    }
}