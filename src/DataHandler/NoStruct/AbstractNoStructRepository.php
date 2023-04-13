<?php

namespace CraftyDigit\Puff\DataHandler\NoStruct;

abstract class AbstractNoStructRepository implements NoStructRepositoryInterface
{
    public function __construct(protected readonly string $dataSourceName)
    {}
}