<?php

namespace CraftyDigit\Puff\EventDispatcher;

use Psr\EventDispatcher\StoppableEventInterface;

class AbstractStoppableEvent extends AbstractEvent implements StoppableEventInterface
{
    public function __construct(
        public object $target,
        protected bool $isPropagationStopped = false
    )
    {
        parent::__construct($target);
    }

    public function isPropagationStopped(): bool
    {
        return $this->isPropagationStopped;
    }

    public function setPropagationStopped(bool $stop = true): void
    {
        $this->isPropagationStopped = $stop;
    }
}