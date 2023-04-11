<?php

namespace CraftyDigit\Puff\DataHandler\NonORM;

abstract class AbstractNonORMRepository implements NonORMRepositoryInterface
{
    public function __construct(protected readonly string $dataSourceName)
    {}
}