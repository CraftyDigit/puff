<?php

namespace CraftyDigit\Puff\ErrorReporter;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Enums\AppMode;
use CraftyDigit\Puff\Http\HttpManagerInterface;
use CraftyDigit\Puff\Router\RouterInterface;
use ErrorException;

readonly class ErrorReporter implements ErrorReporterInterface
{
    public function __construct(
        private Config $config,
        private RouterInterface $router,
        private HttpManagerInterface $httpManager,
    )
    {}

    /**
     * This method enables correct error handling and reporting
     */
    public function registerHandlers(): void
    {
        error_reporting(E_ALL);

        if ( AppMode::from($this->config->mode) === AppMode::PROD) {
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

    public function exceptionHandler($e): void
    {
        error_log($e);

        $errorCode = $e->getCode();

        if ($errorCode === 404) {
            http_response_code(404);
        } else {
            http_response_code(500);
        }

        if (filter_var(ini_get('display_errors'), FILTER_VALIDATE_BOOLEAN)) {
            // TODO: maybe make a component for this?
            if (extension_loaded('xdebug') && $this->config->xdebug['use_exception_message']){
                $debugMessage = '<table>'.$e->xdebug_message.'</table>';
            } else {
                $debugMessage =  $e;
            }

            echo $debugMessage;
        } else {
            $response = $this->router->followRouteByName($errorCode === 404 ? 'error_404' : 'error_500');
            $this->httpManager->sendResponse($response);
        }
    }

    public function errorHandler($level, $message, string $file = '', int $line = 0): void
    {
        $e = new ErrorException($message, 0, $level, $file, $line);
        $this->exceptionHandler($e);
    }

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