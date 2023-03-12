<?php

namespace CraftyDigit\Puff\Exceptions;

use Exception;
use Throwable;

class FileNotFoundException extends Exception
{
    /**
     * @param $message
     * @param $code
     * @param Throwable|null $previous
     * @param string|null $path
     */
    public function __construct($message = "", $code = 500, Throwable $previous = null, string $path = null)
    {
        if ($message === "") {
            if ($path === null) {
                $message = 'File could not be found.';
            } else {
                $message = sprintf('File "%s" could not be found.', $path);
            }
        }

        parent::__construct($message, $code, $previous);
    }
}