<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\DataHandler\DataHandlerInterface;
use CraftyDigit\Puff\EntityManager\NoStruct\NoStructEntityManagerInterface;
use CraftyDigit\Puff\Enums\DataSourceType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class AbstractDataController extends AbstractController
{
    public function __construct(
        protected ContainerExtendedInterface $container,
        protected readonly DataHandlerInterface $dataHandler,
        protected NoStructEntityManagerInterface|EntityManagerInterface|null $entityManager = null
    )
    {
        $this->container->callMethod(parent::class, '__construct', target:  $this);
        
        $this->setEntityManager();
    }

    private function setEntityManager(?DataSourceType $dataSourceType = null): void
    {
        if (!$dataSourceType) {
            $dataSourceType = DataSourceType::tryFrom($this->config->default_data_handler);

            if ($dataSourceType === null) {
                throw new Exception('Invalid default data handler');
            }
        }

        $this->entityManager = $this->dataHandler->getEntityManager($dataSourceType);
    }
}