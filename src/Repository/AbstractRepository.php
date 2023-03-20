<?php

namespace CraftyDigit\Puff\Repository;

abstract class AbstractRepository implements RepositoryInterface
{
    public function __construct(protected readonly string $dataSourceName)
    {}
}