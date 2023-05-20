<?php

namespace CraftyDigit\Puff\Events;

use CraftyDigit\Puff\EventDispatcher\AbstractStoppableEvent;

class GenericEvent extends AbstractStoppableEvent
{
    public function __construct(
        public readonly string $explicitName,
        object $target,
        bool $isPropagationStopped = false
    )
    {
        parent::__construct($target, $isPropagationStopped);
    }
}