<?php

namespace CraftyDigit\Puff\Router;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RouterInterface
{
    public function registerRoutes(): void;
    
    public function followRoute(string $url, ?RequestInterface $request): ResponseInterface;

    public function followRouteByName(string $name, ?RequestInterface $request): ResponseInterface;
}