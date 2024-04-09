<?php

namespace CraftyDigit\Puff\Http;

use CraftyDigit\Puff\Common\Contracts\ResourceManagerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface MiddlewareManagerInterface extends RequestHandlerInterface, ResourceManagerInterface
{
    public function addMiddleware(string $name, MiddlewareInterface $middleware): void;

    public function removeMiddleware(string $name): void;

    public function getMiddlewares(): array;
}