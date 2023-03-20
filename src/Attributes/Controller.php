<?php

namespace CraftyDigit\Puff\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Controller
{
    /**
     * @param string $name
     */
    public function __construct(
        public string $name
    ) {}
}