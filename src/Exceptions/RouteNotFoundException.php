<?php

namespace CraftyDigit\Puff\Exceptions;

use Exception;
use Throwable;

class RouteNotFoundException extends Exception
{
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