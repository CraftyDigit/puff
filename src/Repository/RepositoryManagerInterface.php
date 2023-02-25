<?php

namespace CraftyDigit\Puff\Repository;

interface RepositoryManagerInterface
{
    /**
     * @param string $dataSourceName
     * @return RepositoryInterface
     */
    public function getRepository(string $dataSourceName): RepositoryInterface;
}