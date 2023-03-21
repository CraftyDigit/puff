<?php

namespace CraftyDigit\Puff\Repository;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Exceptions\ClassNotFoundException;

class RepositoryManager implements RepositoryManagerInterface
{
    /**
     * @param Config $config
     * @param ContainerExtendedInterface $container
     * @param string $dataSourceType
     */
    public function __construct(
        private readonly Config $config,
        private readonly ContainerExtendedInterface $container,
        private string $dataSourceType = '',
    )
    {
        $this->dataSourceType = $this->dataSourceType ?: $this->config->data_source_type;
    }

    /**
     * @param string $dataSourceName
     * @return AbstractRepository
     * @throws ClassNotFoundException
     */
    public function getRepository(string $dataSourceName): AbstractRepository
    {
        $repositoryClass = __NAMESPACE__ .'\\'. strtoupper($this->dataSourceType) . 'Repository';

        if (!class_exists($repositoryClass)) {
            throw new ClassNotFoundException("Repository class '$repositoryClass' not found");
        }
        
        return $this->container->get($repositoryClass, ['dataSourceName' => $dataSourceName]);
    }
}