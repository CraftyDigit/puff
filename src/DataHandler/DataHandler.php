<?php

namespace CraftyDigit\Puff\DataHandler;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\EntityManager\NoStruct\JSON\JSONEntityManager;
use CraftyDigit\Puff\EntityManager\NoStruct\NoStructEntityManagerInterface;
use CraftyDigit\Puff\Enums\DataSourceType;
use CraftyDigit\Puff\Exceptions\ClassNotFoundException;
use CraftyDigit\Puff\Exceptions\ConfigParamException;
use CraftyDigit\Puff\Helper;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Cache\DefaultCacheFactory;
use Doctrine\ORM\Cache\RegionsConfiguration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

readonly class DataHandler implements DataHandlerInterface
{
    public function __construct(
        protected ContainerExtendedInterface $container,
        protected Helper                     $helper,
        protected Config                     $config,
    )
    {}
    
    public function getEntityManager(DataSourceType $dataSourceType): NoStructEntityManagerInterface|EntityManagerInterface
    {
        switch ($dataSourceType) {
            case DataSourceType::JSON :
                $entityManager = $this->container->get(JSONEntityManager::class);
                break;
                
            case DataSourceType::DOCTRINE :
                $doctrineConfig = $this->config->doctrine;

                $connectConfig = ORMSetup::createAttributeMetadataConfiguration(
                    paths: [$this->helper->getPathToSrcSubDirectory('Entities')],
                    isDevMode: $this->config->mode === 'dev',
                );
                
                if ($doctrineConfig['cache_enabled']) {
                    /** @var FilesystemAdapter $cache */
                    $cache = $this->container->get(
                        FilesystemAdapter::class,
                        [
                            'defaultLifetime' => $doctrineConfig['cache_lifetime'],
                            'directory' => $this->helper->getPathToAppSubDirectory($doctrineConfig['cache_dir'])
                        ]
                    );

                    $connectConfig->setQueryCache($cache);
                    $connectConfig->setResultCache($cache);

                    /** @var RegionsConfiguration $cache2LConfig */
                    $cache2LConfig = $this->container->get(
                        RegionsConfiguration::class, 
                        ['defaultLifetime' => $doctrineConfig['cache_lifetime']]
                    );

                    /** @var DefaultCacheFactory $cacheFactory */
                    $cacheFactory = $this->container->get(DefaultCacheFactory::class, [$cache2LConfig, $cache]);

                    // Enable second-level-cache
                    $connectConfig->setSecondLevelCacheEnabled();
                    // Cache factory
                    $connectConfig->getSecondLevelCacheConfiguration()
                        ->setCacheFactory($cacheFactory);    
                }

                $dbConfig = $this->config->db;

                if ($dbConfig === null) {
                    throw new ConfigParamException('db');
                }

                $connection = DriverManager::getConnection(
                    params: [
                        'driver' => 'pdo_mysql',
                        'user' => $dbConfig['user'],
                        'password' => $dbConfig['password'],
                        'dbname' => $dbConfig['dbname'],
                        'host' => $dbConfig['host'],
                        'port' => $dbConfig['port'],
                    ],
                    config: $connectConfig,
                );

                $entityManager = $this->container->get(EntityManager::class, ['conn' => $connection, 'config' => $connectConfig]);
                break;
                
            default:
                throw new ClassNotFoundException('Data handler for ' . $dataSourceType->name . ' not registered');
        }
        
        return $entityManager;
    }
    
}