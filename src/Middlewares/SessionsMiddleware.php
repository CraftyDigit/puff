<?php

namespace CraftyDigit\Puff\Middlewares;

use CraftyDigit\Puff\Attributes\Middleware;
use CraftyDigit\Puff\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[Middleware('sessions')]
class SessionsMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected readonly SessionInterface $session
    )
    {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->session->start();

        return $handler->handle($request);
    }
}