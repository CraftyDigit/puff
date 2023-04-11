<?php

namespace CraftyDigit\Puff\SimpleModel;

interface SimpleModelInterface
{

    public function __get(string $name): mixed;

    public function __set(string $name, mixed $value): void;

    public function getData(): array;
}