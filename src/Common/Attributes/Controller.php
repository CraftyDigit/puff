<?php

namespace CraftyDigit\Puff\Common\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Controller
{
    public function __construct(
        public string $name
    ) {}
}