<?php

namespace CraftyDigit\Puff\EventDispatcher;

use CraftyDigit\Puff\Common\Contracts\ResourceManagerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

interface ListenerManagerInterface extends ListenerProviderInterface, ResourceManagerInterface
{
    public function setListenerForEvent(string $eventName, callable $listener, int $priority): void;
}