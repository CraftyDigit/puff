<?php

namespace CraftyDigit\Puff\Container;

use Psr\Container\ContainerInterface;

interface ContainerExtendedInterface extends ContainerInterface
{
    public function get(string $id, array $params = []): mixed;

    public function set(string $id, string $name): void;

    /**
     * This method is used to resolve the dependencies of a method and call it.
     * Examples:
     *  - you need to call a method of a class that is not a service;
     *  - you need to call a parent constructor method from a child class constructor method;
     *  - etc.
     */
    public function callMethod(
        string $class,
        string $method,
        array $params = [],
        string|object|null $target = null
    ): mixed;
}