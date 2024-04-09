<?php

namespace CraftyDigit\Puff\Common\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Controller
{
    // TODO: move all attributes to folder where their referring classes are located

    public function __construct(
        public string $name
    ) {}
}