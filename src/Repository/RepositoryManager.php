<?php

namespace CraftyDigit\Puff\Repository;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Exceptions\ClassNotFoundException;

class RepositoryManager implements RepositoryManagerInterface
{
    /**
     * @param string $dataSourceType
     * @param Config|null $config
     */
    public function __construct(
        protected string $dataSourceType = '',
        protected ?Config $config = null
    )
    {
        $this->config = $this->config ?? Config::getInstance();
        $this->dataSourceType = $dataSourceType ?: $this->config->data_source_type;
    }

    /**
     * @param string $dataSourceName
     * @return RepositoryInterface
     * @throws ClassNotFoundException
     */
    public function getRepository(string $dataSourceName): RepositoryInterface
    {
        $repositoryClass = __NAMESPACE__ .'\\'. strtoupper($this->dataSourceType) . 'Repository';

        if (!class_exists($repositoryClass)) {
            throw new ClassNotFoundException("Repository class '$repositoryClass' not found");
        }

        return new $repositoryClass($dataSourceName);
    }
}