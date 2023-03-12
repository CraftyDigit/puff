<?php

namespace CraftyDigit\Puff\Exceptions;

use Exception;
use Throwable;

class ClassNotFoundException extends Exception
{
    /**
     * @param $message
     * @param $code
     * @param Throwable|null $previous
     * @param string|null $className
     */
    public function __construct($message = "", $code = 500, Throwable $previous = null, string $className = null)
    {
        if ($message === "") {
            if ($className === null) {
                $message = 'Class not found.';
            } else {
                $message = sprintf('Class "%s" could not be found.', $className);
            }
        }

        parent::__construct($message, $code, $previous);
    }
}