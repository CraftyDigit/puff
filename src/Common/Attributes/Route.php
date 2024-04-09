<?php

namespace CraftyDigit\Puff\Common\Attributes;

use Attribute;
use CraftyDigit\Puff\Common\Enums\RequestMethod;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class Route
{
    public RequestMethod $requestMethod;

    public function __construct(
        public string  $path,
        public string   $name,
        RequestMethod | string $requestMethod = RequestMethod::GET,
        public bool $isPublic = true
    )
    {
        $this->requestMethod = $requestMethod instanceof RequestMethod ?
            $requestMethod : RequestMethod::from($requestMethod); 
    }
}