<?php

namespace CraftyDigit\Puff;

use CraftyDigit\Puff\ErrorReporter\ErrorReporterInterface;
use CraftyDigit\Puff\EventDispatcher\ListenerManagerInterface;
use CraftyDigit\Puff\Http\MiddlewareManagerInterface;
use CraftyDigit\Puff\Http\HttpManager;
use CraftyDigit\Puff\Router\RouterInterface;
use CraftyDigit\Puff\Session\SessionInterface;

final readonly class Kernel
{
    public function __construct(
        private ListenerManagerInterface $listenerManager,
        private MiddlewareManagerInterface $middlewareManager,
        private RouterInterface $router,
        private SessionInterface $session,
        private HttpManager $httpManager,
        private ErrorReporterInterface $errorReporter
    )
    {}

    /**
     * This method will start the app
     */
    function start(): void
    {
        $this->errorReporter->registerHandlers();
        $this->listenerManager->registerListeners();
        $this->middlewareManager->registerMiddlewares();
        $this->router->registerRoutes();
        $this->httpManager->setServerRequestFromDefault();
        $this->session->start();
        $this->router->followRoute();
    }
}