<?php

namespace CraftyDigit\Puff\EntityManager\NoStruct;

interface NoStructEntityManagerInterface
{
    public function getRepository(string $dataSourceName): NoStructRepositoryInterface;
}