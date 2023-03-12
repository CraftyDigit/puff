<?php

namespace CraftyDigit\Puff\Router;

use CraftyDigit\Puff\Enums\RequestMethod;

interface RouterInterface
{
    /**
     * @param array $requestParams
     * @return void
     */
    public function followRoute(array $requestParams = []): void;
    
    /**
     * @param string $name
     * @param array $requestParams
     * @return void
     */
    public function followRouteByName(string $name, array $requestParams = []): void;

    /**
     * @param string $path
     * @param array $requestParams
     * @param RequestMethod $method
     * @return void
     */
    public function redirect(string $path, array $requestParams = [], RequestMethod $method = RequestMethod::GET): void;

    /**
     * @param string $name
     * @param array $requestParams
     * @return void
     */
    public function redirectToRouteByName(string $name, array $requestParams = []): void;
}