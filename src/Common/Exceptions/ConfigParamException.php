<?php

namespace CraftyDigit\Puff\Common\Exceptions;

use Exception;
use Throwable;

class ConfigParamException extends Exception
{
    public function __construct(
        $message = "", $code = 404, 
        Throwable $previous = null, 
        ?string $configParamName = null, 
        ?string $configParamValue = null
    )
    {
        if ($message === "") {
            if ($configParamName === null) {
                $message = 'Config param not found or invalid.';
            } else if (!$configParamValue) { 
                $message = sprintf('Config param %s not found or invalid', $configParamName);
            } else {
                $message = sprintf('Config param %s not found or it\'s value "%s" is invalid', $configParamName, $configParamValue);
            }
        }

        parent::__construct($message, $code, $previous);
    }    
}