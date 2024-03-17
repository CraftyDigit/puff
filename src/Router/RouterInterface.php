<?php

namespace CraftyDigit\Puff\Router;

use CraftyDigit\Puff\Enums\RequestMethod;
use Psr\Http\Message\ResponseInterface;

interface RouterInterface
{
    public function registerRoutes(): void;
    
    public function followRoute(string $url, array $requestParams = [], RequestMethod $requestMethod = RequestMethod::GET): ResponseInterface;
    
    public function followRouteByName(string $name, array $requestParams = []): ResponseInterface;
}