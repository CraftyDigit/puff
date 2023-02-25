<?php

namespace CraftyDigit\Puff\Model;

class Model implements ModelInterface
{
    /**
     * @param array $data
     */
    public function __construct(private array $data)
    {}

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->data[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}