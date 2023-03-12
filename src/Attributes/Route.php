<?php

namespace CraftyDigit\Puff\Attributes;

use Attribute;
use CraftyDigit\Puff\Enums\RequestMethod;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    /**
     * @var RequestMethod 
     */
    public readonly RequestMethod $requestMethod;

    /**
     * @param string $path
     * @param string $name
     * @param RequestMethod|string $requestMethod
     * @param bool $isPublic
     */
    public function __construct(
        public readonly string $path,
        public readonly string $name,
        RequestMethod | string $requestMethod = RequestMethod::GET,
        public readonly bool $isPublic = true
    )
    {
        $this->requestMethod = $requestMethod instanceof RequestMethod ?
            $requestMethod : RequestMethod::from($requestMethod); 
    }
}