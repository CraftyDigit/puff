<?php

namespace CraftyDigit\Puff\Middleware;

use CraftyDigit\Puff\Common\Attributes\Middleware;
use CraftyDigit\Puff\Common\Attributes\Singleton;
use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Helper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;
use RuntimeException;

#[Singleton]
class MiddlewareManager implements MiddlewareManagerInterface
{
    public function __construct(
        protected readonly Helper $helper,
        protected readonly ContainerExtendedInterface $container,
        protected readonly Config $config,
        protected ?RequestHandlerInterface $defaultHandler = null,
        protected array $middlewares = [],
        protected array $queue = [],
    )
    {
        $this->registerResources();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (is_null($this->defaultHandler)) {
            throw new RuntimeException('Default handler is not set for middleware');
        }

        if (empty($this->queue)) {
            return $this->defaultHandler->handle($request);
        }

        $middleware = array_shift($this->queue);

        return $middleware->process($request, $this);
    }

    public function registerResources(): void
    {
        $this->registerFromAttributes();
    }

    private function registerFromAttributes(): void
    {
        $filesNames = [];

        foreach ($this->helper->getSrcDirectoryFiles('Middlewares') as $fileName) {
            $filesNames[] = 'App' . $fileName;
        }

        $puffMiddlewaresDirectory = 'Middleware' . DIRECTORY_SEPARATOR . 'Middlewares';
        foreach ($this->helper->getPuffDirectoryFiles($puffMiddlewaresDirectory) as $fileName) {
            $filesNames[] = 'CraftyDigit\Puff' . $fileName;
        }

        foreach ($filesNames as $fileName) {
            $middlewareClass = str_replace('.php', '', $fileName);
            $middlewareClass = str_replace(DIRECTORY_SEPARATOR, '\\', $middlewareClass);

            $reflector = new ReflectionClass($middlewareClass);

            if ($reflector->isInstantiable() && $reflector->isSubclassOf(MiddlewareInterface::class)) {
                $middleware = $this->container->get($middlewareClass);
                $attributes = $reflector->getAttributes(Middleware::class);

                foreach ($attributes as $attribute) {
                    $attributeObject = $attribute->newInstance();
                    $disabledMiddlewares = $this->config->middlewares['disabled'] ?? [];

                    if (in_array($attributeObject->name, $disabledMiddlewares)) {
                        continue;
                    }

                    $this->addMiddleware($attributeObject->name, $middleware);
                }
            }
        }
    }

    public function addMiddleware(string $name, MiddlewareInterface $middleware): void
    {
        $this->middlewares[$name] = $middleware;
    }

    public function removeMiddleware(string $name): void
    {
        unset($this->middlewares[$name]);
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function handleMiddlewares(ServerRequestInterface $request, RequestHandlerInterface $defaultHandler): ResponseInterface
    {
        // TODO: come up with better name for this method
        $this->setDefaultHandler($defaultHandler);
        $this->setQueue($this->middlewares);

        return $this->handle($request);
    }

    public function setDefaultHandler(RequestHandlerInterface $defaultHandler): void
    {
        $this->defaultHandler = $defaultHandler;
    }

    public function setQueue(array $queue): void
    {
        foreach ($queue as $name => $middleware) {
            if (!$middleware instanceof MiddlewareInterface) {
                throw new RuntimeException('Middleware must implement MiddlewareInterface. Invalid middleware: ' . $name);
            }
        }

        $this->queue = $queue;
    }
}