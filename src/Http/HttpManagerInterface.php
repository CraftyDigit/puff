<?php

namespace CraftyDigit\Puff\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface HttpManagerInterface extends RequestHandlerInterface
{
    /*
     * Return the PSR Http Client instance.
     */
    public function getClient(string $clientClass, array $options): ClientInterface;
    
    /*
     * Return the PSR Http ServerRequest instance.
     */
    public function getServerRequest(): ServerRequestInterface;
    
    /**
     * Set the PSR Http Server Request instance.
     */
    public function setServerRequest(ServerRequestInterface $serverRequest): void;

    /**
     * Set the PSR Http ServerRequest with default values. In most cases, this will be the values the globals.
     */
    public function setServerRequestFromDefault(): void;

    /**
     * Send the PSR Http Response
     */
    public function sendResponse(ResponseInterface $response): void;
}