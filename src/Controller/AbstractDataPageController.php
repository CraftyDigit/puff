<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\DataHandler\DataHandlerInterface;
use CraftyDigit\Puff\EntityManager\NoStruct\NoStructEntityManagerInterface;
use CraftyDigit\Puff\Traits\SetEntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;

class AbstractDataPageController extends AbstractPageController
{
    use SetEntityManagerTrait;
    
    public function __construct(
        protected ContainerExtendedInterface $container,
        protected readonly DataHandlerInterface $dataHandler,
        protected NoStructEntityManagerInterface|EntityManagerInterface|null $entityManager = null
    )
    {
        $this->container->callMethod(parent::class, '__construct', target:  $this);
        
        $this->setEntityManager(
            $this->config,
            $this->dataHandler,
            $this->entityManager,
        );
    }
}