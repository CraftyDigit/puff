<?php

namespace CraftyDigit\Puff\Model;

class Model implements ModelInterface
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

    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function getData(): array
    {
        return $this->data;
    }
}