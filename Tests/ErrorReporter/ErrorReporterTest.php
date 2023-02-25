<?php

namespace CraftyDigit\Puff\Tests\ErrorReporter;

use CraftyDigit\Puff\ErrorReporter\ErrorReporter;
use CraftyDigit\Puff\ErrorReporter\ErrorReporterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

final class ErrorReporterTest extends TestCase
{
    /**
     * @var ErrorReporterInterface|MockObject
     */
    public ErrorReporterInterface|MockObject $errorReporter;

    /**
     * @var ReflectionClass 
     */
    public ReflectionClass $errorReporterReflector;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->errorReporter = $this->getMockBuilder(ErrorReporter::class)
            ->onlyMethods([])
            ->getMock();

        $this->errorReporter->setHandlers();

        $this->errorReporterReflector = new ReflectionClass(ErrorReporter::class);
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testExceptionHandler(): void
    {
        $exceptionHandler = $this->errorReporterReflector->getMethod('exceptionHandler');
        $exceptionHandlerName = $exceptionHandler->getName();

        $currentExceptionHandler = set_exception_handler(function () {});
        $currentExceptionHandlerName = $currentExceptionHandler[1];

        restore_exception_handler();

        $this->assertSame($currentExceptionHandlerName, $exceptionHandlerName);
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testErrorHandler(): void
    {
        $errorHandler = $this->errorReporterReflector->getMethod('errorHandler');
        $errorHandlerName = $errorHandler->getName();

        $currentErrorHandler = set_error_handler(function () {});
        $currentErrorHandlerName = $currentErrorHandler[1];

        restore_error_handler();

        $this->assertSame($currentErrorHandlerName, $errorHandlerName);
    }
}