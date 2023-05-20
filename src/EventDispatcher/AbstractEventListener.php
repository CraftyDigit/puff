<?php

namespace CraftyDigit\Puff\EventDispatcher;

abstract class AbstractEventListener
{
    abstract public function __invoke(AbstractEvent $event): void;
}