<?php

namespace CraftyDigit\Puff\EventDispatcher;

use CraftyDigit\Puff\EventDispatcher\AbstractStoppableEvent;

class GenericEvent extends AbstractStoppableEvent
{
    public function __construct(
        public readonly string $explicitName,
        public object $target,
        protected bool $isPropagationStopped = false
    )
    {
        parent::__construct($target, $isPropagationStopped);
    }
}