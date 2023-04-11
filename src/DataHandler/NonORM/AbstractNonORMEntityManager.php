<?php

namespace CraftyDigit\Puff\DataHandler\NonORM;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;

/**
 * This class intended to be used for non-ORM data sources
 */
abstract class AbstractNonORMEntityManager implements NonORMEntityManagerInterface
{
    public function __construct(
        protected readonly ContainerExtendedInterface $container,
    )
    {}
}