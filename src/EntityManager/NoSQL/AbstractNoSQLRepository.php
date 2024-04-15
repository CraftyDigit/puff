<?php

namespace CraftyDigit\Puff\EntityManager\NoSQL;

abstract class AbstractNoSQLRepository implements NoSQLRepositoryInterface
{
    public function __construct(protected readonly string $dataSourceName)
    {}
}