<?php

namespace CraftyDigit\Puff\Tests;

use CraftyDigit\Puff\ErrorReporter\ErrorReporter;
use CraftyDigit\Puff\ErrorReporter\ErrorReporterInterface;
use CraftyDigit\Puff\Kernel;
use CraftyDigit\Puff\Router\Router;
use CraftyDigit\Puff\Router\RouterInterface;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class KernelTest extends TestCase
{
    /**
     * @var ErrorReporterInterface|MockObject
     */
    public ErrorReporterInterface|MockObject $errorReporterMock;

    /**
     * @var RouterInterface|MockObject
     */
    public RouterInterface|MockObject $routerMock;

    /**
     * @var Kernel
     */
    public Kernel $kernel;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->errorReporterMock = $this->createMock(ErrorReporter::class);
        $this->routerMock = $this->createMock(Router::class);
        $this->kernel = new Kernel($this->errorReporterMock, $this->routerMock);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testStart()
    {
        $this->errorReporterMock->expects($this->once())
            ->method('setHandlers');

        $this->routerMock->expects($this->once())
            ->method('followRoute');

        $this->kernel->start();
    }
}
