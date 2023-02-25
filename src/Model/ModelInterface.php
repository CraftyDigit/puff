<?php

namespace CraftyDigit\Puff\Model;

interface ModelInterface
{

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed;

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void;

    /**
     * @return array
     */
    public function getData(): array;
}