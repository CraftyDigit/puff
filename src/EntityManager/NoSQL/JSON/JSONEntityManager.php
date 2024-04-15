<?php

namespace CraftyDigit\Puff\EntityManager\NoSQL\JSON;

use CraftyDigit\Puff\EntityManager\NoSQL\AbstractNoSQLEntityManager;
use CraftyDigit\Puff\EntityManager\NoSQL\NoSQLRepositoryInterface;

class JSONEntityManager extends AbstractNoSQLEntityManager
{
    public function getRepository(string $dataSourceName): NoSQLRepositoryInterface
    {
        return $this->container->get(JSONRepository::class, ['dataSourceName' => $dataSourceName]);
    }
}