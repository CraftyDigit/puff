<?php

namespace CraftyDigit\Puff\Router;

use CraftyDigit\Puff\Common\Contracts\ResourceManagerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RouteManagerInterface extends ResourceManagerInterface
{
    public function followRoute(string $url, ?RequestInterface $request): ResponseInterface;

    public function followRouteByName(string $name, ?RequestInterface $request): ResponseInterface;
}