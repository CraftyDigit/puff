<?php

namespace CraftyDigit\Puff\DataHandler\NoStruct\JSON;

use CraftyDigit\Puff\DataHandler\NoStruct\AbstractNoStructEntityManager;
use CraftyDigit\Puff\DataHandler\NoStruct\NoStructRepositoryInterface;

class JSONEntityManager extends AbstractNoStructEntityManager
{
    public function getRepository(string $dataSourceName): NoStructRepositoryInterface
    {
        return $this->container->get(JSONRepository::class, ['dataSourceName' => $dataSourceName]);
    }
}