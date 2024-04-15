<?php

namespace CraftyDigit\PuffTests\Utils;

use CraftyDigit\Puff\Common\Enums\RequestMethod;
use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\Container;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\ErrorReporter\ErrorReporterInterface;
use CraftyDigit\Puff\Http\HttpManager;
use CraftyDigit\Puff\Http\HttpManagerInterface;
use CraftyDigit\Puff\Kernel;
use CraftyDigit\Puff\Middleware\MiddlewareManagerInterface;
use CraftyDigit\Puff\Router\RouteManagerInterface;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PuffTestCase extends TestCase
{
    public Container $container;
    public RouteManagerInterface $routeManager;
    public HttpManagerInterface|MockObject $httpManagerMock;
    public ErrorReporterInterface|MockObject $errorReporterMock;
    public Kernel $kernel;

    protected function prepareKernel(string $routeName): void
    {
        $this->container = Container::getInstance();
        $this->routeManager = $this->container->get(RouteManagerInterface::class);

        $this->errorReporterMock = $this->createMock(ErrorReporterInterface::class);

        $this->httpManagerMock = $this->getMockBuilder(HttpManager::class)
            ->setConstructorArgs([
                $this->container->get(Config::class),
                $this->container->get(ContainerExtendedInterface::class),
                $this->container->get(RouteManagerInterface::class),
                $this->container->get(MiddlewareManagerInterface::class),
                null,
            ])
            ->disableOriginalClone()
            ->onlyMethods(['setServerRequestFromDefault'])
            ->getMock();

        $testRoute = $this->routeManager->getRouteByName($routeName);

        $this->httpManagerMock->method('setServerRequestFromDefault')
            ->willReturnCallback(function() use ($testRoute) {
                /* @var RequestMethod $requestMethod */
                $requestMethod = $testRoute['requestMethod'];

                $method = $requestMethod->value;
                $headers = [];
                $uri = $testRoute['path'];
                $body = null;
                $protocol = '';

                $serverRequest = new ServerRequest($method, $uri, $headers, $body, $protocol, $_SERVER);

                $this->httpManagerMock->setServerRequest($serverRequest);
            });

        $this->kernel = $this->container->get(Kernel::class, [
            'errorReporter' => $this->errorReporterMock,
            'httpManager' => $this->httpManagerMock,
        ]);
    }
}