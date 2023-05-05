<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\DataHandler\DataHandlerInterface;
use CraftyDigit\Puff\EntityManager\NoStruct\NoStructEntityManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use CraftyDigit\Puff\Traits\DataControllerTrait;

class AbstractDataController extends AbstractController
{
    use DataControllerTrait;
    
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