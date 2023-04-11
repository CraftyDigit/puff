<?php

namespace CraftyDigit\Puff\DataHandler\NonORM;

interface NonORMEntityManagerInterface
{
    public function getRepository(string $dataSourceName): NonORMRepositoryInterface;
}