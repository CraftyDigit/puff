<?php

namespace CraftyDigit\Puff\EntityManager\NoStruct;

abstract class AbstractNoStructRepository implements NoStructRepositoryInterface
{
    public function __construct(protected readonly string $dataSourceName)
    {}
}