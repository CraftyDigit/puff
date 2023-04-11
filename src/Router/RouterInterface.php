<?php

namespace CraftyDigit\Puff\Router;

use CraftyDigit\Puff\Enums\RequestMethod;

interface RouterInterface
{
    public function followRoute(array $requestParams = []): void;
    
    public function followRouteByName(string $name, array $requestParams = []): void;

    public function redirect(string $path, array $requestParams = [], RequestMethod $method = RequestMethod::GET): void;

    public function redirectToRouteByName(string $name, array $requestParams = []): void;
}