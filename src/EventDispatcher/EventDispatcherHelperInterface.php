<?php

namespace CraftyDigit\Puff\EventDispatcher;

interface EventDispatcherHelperInterface
{
    public function dispatchInNewEvent(object $target, string $eventName, string $eventClass = GenericEvent::class): object;
}