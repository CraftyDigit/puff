<?php

namespace CraftyDigit\Puff\DataHandler;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\EntityManager\NoStruct\JSON\JSONEntityManager;
use CraftyDigit\Puff\EntityManager\NoStruct\NoStructEntityManagerInterface;
use CraftyDigit\Puff\Enums\DataSourceType;
use CraftyDigit\Puff\Exceptions\ClassNotFoundException;
use CraftyDigit\Puff\Helper;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

readonly class DataHandler implements DataHandlerInterface
{
    public function __construct(
        protected ContainerExtendedInterface $container,
        protected Helper                     $helper,
        protected Config                     $config,
    )
    {}
    
    public function getEntityManager(DataSourceType $dataHandler = DataSourceType::DOCTRINE): NoStructEntityManagerInterface|EntityManagerInterface
    {
        switch ($dataHandler) {
            case DataSourceType::JSON :
                $entityManager = $this->container->get(JSONEntityManager::class);
                break;
                
            case DataSourceType::DOCTRINE :
                $connectConfig = ORMSetup::createAttributeMetadataConfiguration(
                    paths: [$this->helper->getPathToAppDirectory('Entities')],
                    isDevMode: $this->config->mode === 'dev',
                );

                $dbConfig = $this->config->db;

                if ($dbConfig === null) {
                    throw new \Exception('DB config is not set');
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
                throw new ClassNotFoundException('Data handler for ' . $dataHandler->name . ' not registered');
        }
        
        return $entityManager;
    }
    
}