<?php

namespace CraftyDigit\Puff\DataHandler\NoStruct;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;

/**
 * This class intended to be used for unstructured data sources
 */
abstract class AbstractNoStructEntityManager implements NoStructEntityManagerInterface
{
    public function __construct(
        protected readonly ContainerExtendedInterface $container,
    )
    {}
}