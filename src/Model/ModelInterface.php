<?php

namespace CraftyDigit\Puff\Model;

interface ModelInterface
{

    public function __get(string $name): mixed;

    public function __set(string $name, mixed $value): void;

    public function __isset(string $name): bool;

    public function getData(): array;
}