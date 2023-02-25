<?php

namespace CraftyDigit\Puff\Tests\Controller;

use CraftyDigit\Puff\Controller\AbstractController;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AbstractControllerTest extends TestCase
{
    /**
     * @var AbstractController|MockObject
     */
    public AbstractController|MockObject $controller;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->controller = $this->getMockForAbstractClass(AbstractController::class,[],'fooController');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testIfRenderThrowExceptionWhenTemplateFileNotExist(): void
    {
        $this->controller->template = $this->controller->templateManager->getTemplate(
            $this->controller->isAdminController,
            'baz'
        );

        $this->expectException('Exception');
        $this->controller->render();
    }
}