<?php

namespace CraftyDigit\Puff\EventDispatcher;

use CraftyDigit\Puff\Events\GenericEvent;

interface EventDispatcherHelperInterface
{
    public function dispatchInNewEvent(object $target, string $eventName, string $eventClass = GenericEvent::class): object;
}