<?php

namespace CraftyDigit\Puff\EventDispatcher;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Exceptions\ClassNotFoundException;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventDispatcherHelper implements EventDispatcherHelperInterface
{
    public function __construct(
        private readonly ContainerExtendedInterface $container,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {}

    public function dispatchInNewEvent(
        object $target,
        string $eventName,
        string $eventClass = GenericEvent::class
    ):  object
    {
        if (!class_exists($eventClass)) {
            throw new ClassNotFoundException("Event class '$eventClass' not found");
        }

        $event = $this->container->get(
            $eventClass,
            [$eventName, $target]
        );

        $this->eventDispatcher->dispatch($event);

        return $event->target;
    }
}