<?php

namespace CraftyDigit\Puff\Traits;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\DataHandler\DataHandlerInterface;
use CraftyDigit\Puff\Enums\DataSourceType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

trait SetEntityManagerTrait
{
    private function setEntityManager(
        Config $config,
        DataHandlerInterface $dataHandler,
        ?EntityManagerInterface &$entityManager,
        ?DataSourceType $dataSourceType = null,
    ): void
    {
        if (!$dataSourceType) {
            $dataSourceType = DataSourceType::tryFrom($config->default_data_handler);

            if ($dataSourceType === null) {
                throw new Exception('Invalid default data handler');
            }
        }

        $entityManager = $dataHandler->getEntityManager($dataSourceType);
    }
}