<?php

namespace CraftyDigit\Puff\EventDispatcher;

use CraftyDigit\Puff\Common\Attributes\EventListener;
use CraftyDigit\Puff\Common\Attributes\Singleton;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Helper;
use ReflectionClass;

#[Singleton]
class ListenerManager implements ListenerManagerInterface
{
    public function __construct(
        protected Helper $helper,
        protected ContainerExtendedInterface $container,
        private array $listeners = [],
    )
    {
        $this->registerResources();
    }
    
    public function registerResources(): void
    {
        $this->registerFromAttributes();
    }

    public function setListenerForEvent(string $eventName, callable $listener, int $priority = 0): void
    {
        $this->listeners[$eventName][$priority][] = $listener;
        ksort($this->listeners[$eventName]);
    }

    public function getListenersForEvent(object $event): iterable
    {
        $eventClass = $event instanceof GenericEvent ? $event->explicitName : get_class($event);

        if (!isset($this->listeners[$eventClass])) {
            return [];
        }

        $listeners = [];

        foreach ($this->listeners[$eventClass] as $listenersByPriority) {
            foreach ($listenersByPriority as $listener) {
                $listeners[] = $listener;
            }
        }

        return array_reverse($listeners);
    }

    protected function registerFromAttributes(): void
    {
        $filesNames = [];
        
        foreach ($this->helper->getSrcDirectoryFiles('EventListeners') as $fileName) {
            $filesNames[] = 'App' . $fileName;    
        }
        
        foreach ($this->helper->getPuffDirectoryFiles('EventListeners') as $fileName) {
            $filesNames[] = 'CraftyDigit\Puff' . $fileName;    
        }

        foreach ($filesNames as $fileName) {
            $eventListenerClass = str_replace('.php', '', $fileName);
            $eventListenerClass = str_replace(DIRECTORY_SEPARATOR, '\\', $eventListenerClass);

            $reflectionClass = new ReflectionClass($eventListenerClass);
            
            if ($reflectionClass->isInstantiable() && $reflectionClass->isSubclassOf(AbstractEventListener::class)) {
                $listener = $this->container->get($eventListenerClass);
                
                $listenerAttributes = $reflectionClass->getAttributes(EventListener::class);
                
                foreach ($listenerAttributes as $listenerAttribute) {
                    $listenerAttribute = $listenerAttribute->newInstance();
                    
                    $this->setListenerForEvent($listenerAttribute->eventName, $listener, $listenerAttribute->priority);
                }
            }
        }
    }
}