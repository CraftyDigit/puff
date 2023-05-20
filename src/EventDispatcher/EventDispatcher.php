<?php

namespace CraftyDigit\Puff\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

readonly class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        protected ListenerManagerInterface $listenerManager
    )
    {}
    
    public function dispatch(object $event): object
    {
        if ($this->isPropagationStopped($event)) {
            return $event;
        };
                
        $listeners = $this->listenerManager->getListenersForEvent($event);
        
        foreach ($listeners as $listener) {
            $listener($event);

            if ($this->isPropagationStopped($event)) {
                break;
            }
        }
        
        return $event;            
    }
    
    private function isPropagationStopped($event): bool 
    {
        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            return true;
        }    
        
        return false;
    }
}