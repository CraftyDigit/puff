<?php

namespace CraftyDigit\Puff\Common\Exceptions;

use Exception;
use Throwable;

class FileNotFoundException extends FileSystemException
{
    public function __construct(
        $message = "", 
        $code = 500, 
        Throwable $previous = null, 
        ?string $path = null
    )
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