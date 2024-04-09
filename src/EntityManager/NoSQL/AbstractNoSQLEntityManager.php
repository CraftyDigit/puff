<?php

namespace CraftyDigit\Puff\EntityManager\NoSQL;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;

/**
 * This class intended to be used for unstructured data sources
 */
abstract class AbstractNoSQLEntityManager implements NoSQLEntityManagerInterface
{
    public function __construct(
        protected readonly ContainerExtendedInterface $container,
    )
    {}
}