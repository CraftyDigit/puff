<?php
namespace CraftyDigit\PuffTests\Tests;

use CraftyDigit\PuffTests\Utils\PuffTestCase;
use Psr\Http\Message\ResponseInterface;

final class KernelTest extends PuffTestCase
{
    const TEST_ROUTE_NAME = 'testMainPage';

    protected function setUp(): void
    {
        $this->prepareKernel(self::TEST_ROUTE_NAME);
    }

    public function testGeneralAppWork(): void
    {
        $testRoute = $this->routeManager->getRouteByName(self::TEST_ROUTE_NAME);
        $testController = $this->container->get($testRoute['controller']);

        /* @var ResponseInterface $testResponse */
        $testResponse = $testController->{$testRoute['method']}();
        $testResponseString = $testResponse->getBody()->__toString();

        $this->expectOutputString($testResponseString);

        $this->kernel->start();
    }
}