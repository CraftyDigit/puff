<?php

namespace CraftyDigit\Puff\Common\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)] 
class EventListener
{
    public function __construct(
        public string $eventName,
        public int $priority
    ) {}
}