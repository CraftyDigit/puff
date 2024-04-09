<?php

namespace CraftyDigit\Puff\EntityManager\NoSQL;

interface NoSQLEntityManagerInterface
{
    public function getRepository(string $dataSourceName): NoSQLRepositoryInterface;
}