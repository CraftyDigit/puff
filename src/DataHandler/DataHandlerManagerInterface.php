<?php

namespace CraftyDigit\Puff\DataHandler;

use CraftyDigit\Puff\DataHandler\NoStruct\NoStructEntityManagerInterface;
use CraftyDigit\Puff\Enums\DataHandler;
use Doctrine\ORM\EntityManagerInterface;

interface DataHandlerManagerInterface
{
    public function getEntityManager(DataHandler $dataHandler): NoStructEntityManagerInterface|EntityManagerInterface;
}