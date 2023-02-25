<?php

namespace CraftyDigit\Puff\Exceptions;

use Exception;
use Throwable;

class UnknownErrorException extends Exception
{
    /**
     * @param $message
     * @param $code
     * @param Throwable|null $previous
     * @param string|null $errorCode
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null, string $errorCode = null)
    {
        if (null === $message) {
            if (null === $errorCode) {
                $message = 'Unknown error was tried to be thrown.';
            } else {
                $message = sprintf('Error with code "%s" is not handled in application.', $errorCode);
            }
        }

        parent::__construct($message, $code, $previous);
    }
}