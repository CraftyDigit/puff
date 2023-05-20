<?php

namespace CraftyDigit\Puff\EventDispatcher;

use Psr\EventDispatcher\ListenerProviderInterface;

interface ListenerManagerInterface extends ListenerProviderInterface
{
    public function setListenerForEvent(string $eventName, callable $listener, int $priority): void;
    
    public function registerListeners(): void;
}