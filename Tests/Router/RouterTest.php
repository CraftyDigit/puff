<?php

namespace CraftyDigit\Puff\Tests\Router;

use CraftyDigit\Puff\Controller\ControllerInterface;
use CraftyDigit\Puff\Controller\ControllerManager;
use CraftyDigit\Puff\Controller\ControllerManagerInterface;
use CraftyDigit\Puff\Controller\AbstractController;
use CraftyDigit\Puff\Router\Router;
use CraftyDigit\Puff\Router\RouterInterface;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    /**
     * @var ControllerInterface|MockObject
     */
    public ControllerInterface|MockObject $controllerMock;

    /**
     * @var ControllerManagerInterface|MockObject
     */
    public ControllerManagerInterface|MockObject $controllerManagerMock;

    /**
     * @var RouterInterface
     */
    public RouterInterface $router;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->controllerMock = $this->getMockForAbstractClass(
            AbstractController::class, [], 'barController', true, true, true, ['render']);

        $this->controllerManagerMock = $this->createMock(ControllerManager::class);
        $this->controllerManagerMock->method('getController')
            ->willReturn($this->controllerMock);

        $this->router = new Router($this->controllerManagerMock);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testFollowRoute(): void
    {
        $this->controllerMock->expects($this->once())
            ->method('render');

        $_SERVER['REQUEST_URI'] = '';

        $this->router->followRoute();

        unset($_SERVER['REQUEST_URI']);
    }
}