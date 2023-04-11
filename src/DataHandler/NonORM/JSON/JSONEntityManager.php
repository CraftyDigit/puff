<?php

namespace CraftyDigit\Puff\DataHandler\NonORM\JSON;

use CraftyDigit\Puff\DataHandler\NonORM\AbstractNonORMEntityManager;
use CraftyDigit\Puff\DataHandler\NonORM\NonORMRepositoryInterface;

class JSONEntityManager extends AbstractNonORMEntityManager
{
    public function getRepository(string $dataSourceName): NonORMRepositoryInterface
    {
        return $this->container->get(JSONRepository::class, ['dataSourceName' => $dataSourceName]);
    }
}