<?php

namespace CraftyDigit\Puff\Container;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Exceptions\Container\ContainerException;
use CraftyDigit\Puff\Exceptions\Container\ServiceNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;

class Container implements ContainerExtendedInterface
{
    /**
     * @param array $services
     * @param array $instances
     * @param Config|null $config
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     */
    private function __construct(
        private array $services = [],
        private array $instances = [],
        private ?Config $config = null
    )
    {
        $this->config = $this->get(Config::class);
        $this->registerConfigServices();
    }

    /**
     * @param array $services
     * @param array $instances
     * @param Config|null $config
     * @return Container
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     */
    public static function getInstance(
        array $services = [],
        array $instances = [],
        ?Config $config = null
    ): Container
    {
        static $instance = null;
        
        if ($instance === null) {
            $instance = new Container(...func_get_args());
        }
        
        return $instance;
    }

    /**
     * Register services from config
     * 
     * @return void
     * @throws ServiceNotFoundException
     */
    private function registerConfigServices(): void
    {
        $configServices = $this->config->di_container['services'] ?? [];

        foreach ($configServices as $id => $name) {
            $this->set($id, $name);
        };
    }

    /**
     * @inheritDoc
     * @param string $id
     * @param array $params
     * @return mixed
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     */
    public function get(string $id, array $params = []): mixed
    {
        $name = $this->has($id) ? $this->services[$id] : $id;
        
        if (!class_exists($name)) {
            throw new ServiceNotFoundException(
                "Service '{$id}' can't be called - corresponding class not found: " . $name
            );
        }

        return $this->callClass($name, $params);
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    /**
     * @param string $id
     * @param string $name
     * @return void
     * @throws ServiceNotFoundException
     */
    public function set(string $id, string $name): void
    {
        if (!class_exists($name)) {
            throw new ServiceNotFoundException(
                "Service '{$id}' can't be registered - corresponding class not found: " . $name
            );
        }
        
        $this->services[$id] = $name;
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     */
    private function callClass(string $name, array $params = []): mixed
    {
        if (array_key_exists($name, $this->instances)) {
            // Service is a singleton class and has already been instantiated. Return the instance.
            return $this->instances[$name];
        }
        
        $reflector = new ReflectionClass($name);
        
        if (!$reflector->isInstantiable()) {
            // Service can"t be instantiated or '__constructor' method is not public
            
            if ($reflector->hasMethod('getInstance')) {
                // Service is a singleton class and has not been instantiated yet
                $constructor = $reflector->getMethod('getInstance');
            }
        }

        $constructor = $constructor ?? $reflector->getConstructor();
        
        if (
            is_null($constructor) 
            && !$reflector->isAbstract()
            && !$reflector->isInterface()
            && !$reflector->isTrait()
        ) {
            // Service has no '__constructor' and no 'getInstance' method. Just instantiate it.
            return $reflector->newInstance();
        }

        $dependencies = $this->resolveMethodDependencies($constructor, $name, $params);

        if ($constructor->getName() === 'getInstance') {
            // Service is a singleton and has not been instantiated yet.
            // Instantiate it with params and save it in the instances array.
            $this->instances[$name] = $constructor->invoke(null, ...$dependencies);
            
            return $this->instances[$name];
        } else {
            // Service is not a singleton and has not been instantiated yet.
            // Instantiate it with params.
            return $reflector->newInstanceArgs($dependencies);
        }
    }

    /**
     * @inheritDoc
     * @param string $class
     * @param string $method
     * @param array $params
     * @param string|object|null $target
     * @return mixed
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     */
    public function callMethod(
        string $class, 
        string $method, 
        array $params = [], 
        string|object|null $target = null
    ): mixed 
    {
        $method = new ReflectionMethod($class, $method);
        
        $dependencies = $this->resolveMethodDependencies($method, $class, $params);
        
        $target = $target ?? $class;
        $target = is_string($target) ? $this->get($target) : $target;
        
        return $method->invoke($target, ...$dependencies);
    }

    /**
     * @param ReflectionMethod $method
     * @param string $name
     * @param array $presetParams
     * @return array
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     */
    private function resolveMethodDependencies(
        ReflectionMethod $method, 
        string $name, 
        array $presetParams = []
    ): array
    {
        $params = $method->getParameters();

        $dependencies = [];

        foreach ($params as $param) {
            $paramName = $param->getName();
            
            if (array_key_exists($paramName, $presetParams)) {
                // Dependency value has been passed in preset params. Use it.
                $dependencies[] = $presetParams[$paramName];
                continue;
            }
            
            $paramType = $param->getType();

            if (!$paramType) {
                throw new ContainerException(
                    "'{$name}' service can't be resolved because '{$paramName}' param type is not defined"
                );
            }

            if ($paramType instanceof ReflectionUnionType) {
                throw new ContainerException(
                    "'{$name}' service can't be resolved because '{$paramName}' param has a union type"
                );
            }

            if ($paramType instanceof ReflectionNamedType) {
                if ($param->isDefaultValueAvailable()) {
                    // Dependency has default value. Use it.
                    $dependencies[] = $param->getDefaultValue();
                } else {
                    // Dependency has no default value.
                    if ($paramType->isBuiltin()) {
                        // Dependency is a primitive type. Throw an exception.
                        throw new ContainerException(
                            "'{$name}' service can't be resolved because '{$paramName}' param is not optional and has no default value"
                        );
                    } else {
                        // Dependency is a class. Resolve it.
                        $dependencies[] = $this->get($paramType->getName());
                    }
                }
            }
        }
        return $dependencies;
    }
}