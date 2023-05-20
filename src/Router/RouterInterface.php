<?php

namespace CraftyDigit\Puff\Router;

use CraftyDigit\Puff\Enums\RequestMethod;

interface RouterInterface
{
    public function registerRoutes(): void;
    
    public function followRoute(?string $url = null, array $requestParams = [], RequestMethod $requestMethod = RequestMethod::GET): void;
    
    public function followRouteByName(string $name, array $requestParams = []): void;
}