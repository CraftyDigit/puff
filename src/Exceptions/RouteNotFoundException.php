<?php

namespace CraftyDigit\Puff\Exceptions;

use CraftyDigit\Puff\Common\Enums\RequestMethod;
use Exception;
use Throwable;

class RouteNotFoundException extends Exception
{
    public function __construct(
        $message = "", 
        $code = 404, 
        Throwable $previous = null, 
        ?string $route = null, 
        RequestMethod $requestMethod = RequestMethod::GET
    )
    {
        if ($message === "") {
            if ($route === null) {
                $message = 'Route not registered.';
            } else {
                $message = sprintf('Route "%s" not registered or can\'t be accessed using "%s" method', $route, $requestMethod->value);
            }
        }

        parent::__construct($message, $code, $previous);
    }
}