<?php

namespace CraftyDigit\Puff\EventDispatcher;

abstract class AbstractEvent
{
    public function __construct(public object $target)
    {}
}