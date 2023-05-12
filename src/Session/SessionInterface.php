<?php

namespace CraftyDigit\Puff\Session;

interface SessionInterface
{
    public function start(): void;

    public function set(string $key, mixed $value): void;

    public function get(string $key): mixed;
}