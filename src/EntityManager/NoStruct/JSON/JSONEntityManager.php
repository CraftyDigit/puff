<?php

namespace CraftyDigit\Puff\EntityManager\NoStruct\JSON;

use CraftyDigit\Puff\EntityManager\NoStruct\AbstractNoStructEntityManager;
use CraftyDigit\Puff\EntityManager\NoStruct\NoStructRepositoryInterface;

class JSONEntityManager extends AbstractNoStructEntityManager
{
    public function getRepository(string $dataSourceName): NoStructRepositoryInterface
    {
        return $this->container->get(JSONRepository::class, ['dataSourceName' => $dataSourceName]);
    }
}