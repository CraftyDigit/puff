<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Enums\ResponseType;
use CraftyDigit\Puff\Http\HttpManagerInterface;
use CraftyDigit\Puff\Router\RouterInterface;
use CraftyDigit\Puff\Session\SessionInterface;

abstract class AbstractController
{
    public function __construct(
        protected ContainerExtendedInterface $container,
        protected readonly RouterInterface $router,
        protected readonly ControllerManagerInterface $controllerManager,
        protected readonly Config $config,
        protected readonly HttpManagerInterface $httpManager,
        protected readonly SessionInterface $session,
    )
    {}
    
    public function respond(
        ?ResponseType $type = null,
        ?int   $code = null,
        string $codeMessage = '',
        array  $headers = [],
        string $data = ''
    ): void
    {
        $response = $this->httpManager->createResponse(
            $type,
            $code,
            $codeMessage,
            $headers,
            $data
        );
        
        $this->httpManager->sendResponse($response);
    }
}