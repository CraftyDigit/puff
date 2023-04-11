<?php

namespace CraftyDigit\Puff\SimpleModel;

class SimpleModel implements SimpleModelInterface
{
    public function __construct(private array $data)
    {}

    public function __get(string $name): mixed
    {
        return $this->data[$name];
    }

    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    public function getData(): array
    {
        return $this->data;
    }
}