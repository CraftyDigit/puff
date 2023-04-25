<?php

namespace CraftyDigit\Puff\DataHandler;

use CraftyDigit\Puff\EntityManager\NoStruct\NoStructEntityManagerInterface;
use CraftyDigit\Puff\Enums\DataSourceType;
use Doctrine\ORM\EntityManagerInterface;

interface DataHandlerInterface
{
    public function getEntityManager(DataSourceType $dataSourceType): NoStructEntityManagerInterface|EntityManagerInterface;
}