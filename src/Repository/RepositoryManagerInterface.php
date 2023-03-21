<?php

namespace CraftyDigit\Puff\Repository;

interface RepositoryManagerInterface
{
    /**
     * @param string $dataSourceName
     * @return AbstractRepository
     */
    public function getRepository(string $dataSourceName): AbstractRepository;
}