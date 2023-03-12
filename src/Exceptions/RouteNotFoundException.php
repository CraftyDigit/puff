<?php

namespace CraftyDigit\Puff\Exceptions;

use Exception;
use Throwable;

class RouteNotFoundException extends Exception
{
    /**
     * @param $message
     * @param $code
     * @param Throwable|null $previous
     * @param string|null $className
     */
    public function __construct($message = "", $code = 404, Throwable $previous = null, string $route = null)
    {
        if ($message === "") {
            if ($route === null) {
                $message = 'Route not registered.';
            } else {
                $message = sprintf('Route "%s" not registered.', $route);
            }
        }

        parent::__construct($message, $code, $previous);
    }
}