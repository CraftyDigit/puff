<?php

namespace CraftyDigit\Puff\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Middleware
{
    public function __construct(
        public string $name
    ) {}
}