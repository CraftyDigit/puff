<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\DataHandler\DataHandlerInterface;
use CraftyDigit\Puff\Enums\ResponseType;
use CraftyDigit\Puff\Http\HttpManagerInterface;
use CraftyDigit\Puff\Router\RouterInterface;
use CraftyDigit\Puff\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractController
{
    public function __construct(
        protected ContainerExtendedInterface $container,
        protected readonly RouterInterface $router,
        protected readonly ControllerManagerInterface $controllerManager,
        protected readonly Config $config,
        protected readonly HttpManagerInterface $httpManager,
        protected readonly SessionInterface $session,
        protected readonly DataHandlerInterface $dataHandler,
        protected ResponseType $defaultResponseType = ResponseType::JSON,
    )
    {}

    public function defaultRespond(string $data = ''): ResponseInterface
    {
        return $this->httpManager->createResponse(
            type: $this->defaultResponseType,
            data: $data,
        );
    }

    public function respond (
        ?ResponseType $type = null,
        ?int   $code = null,
        string $codeMessage = '',
        array  $headers = [],
        string $data = ''
    ): ResponseInterface
    {
        return $this->httpManager->createResponse(
            $type ?? $this->defaultResponseType,
            $code,
            $codeMessage,
            $headers,
            $data
        );
    }
}