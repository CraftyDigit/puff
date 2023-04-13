<?php

namespace CraftyDigit\Puff\DataHandler\NoStruct;

interface NoStructEntityManagerInterface
{
    public function getRepository(string $dataSourceName): NoStructRepositoryInterface;
}