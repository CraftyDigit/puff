<?php

namespace CraftyDigit\Puff\DataHandler;

use CraftyDigit\Puff\Enums\DataHandler;

interface DataHandlerManagerInterface
{
    public function getEntityManager(DataHandler $dataHandler): object;
}