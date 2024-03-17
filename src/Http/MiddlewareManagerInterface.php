<?php

namespace CraftyDigit\Puff\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface MiddlewareManagerInterface extends RequestHandlerInterface
{
    public function addMiddleware(string $name, MiddlewareInterface $middleware): void;

    public function removeMiddleware(string $name): void;

    public function getMiddlewares(): array;
}