<?php

namespace CraftyDigit\Puff\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Controller
{
    /**
     * @param string $name
     */
    public function __construct(
        public readonly string $name
    ) {}
}