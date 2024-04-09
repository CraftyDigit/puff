<?php

namespace CraftyDigit\Puff\Container;

use CraftyDigit\Puff\Common\Attributes\Singleton;
use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Common\Exceptions\ContainerException;
use CraftyDigit\Puff\Common\Exceptions\ServiceNotFoundException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;

class Container implements ContainerExtendedInterface
{
    private function __construct(
        private array $services = [],
        public array $instances = [],
        private ?Config $config = null
    )
    {
        $this->config = $this->get(Config::class);
        $this->registerConfigServices();
    }

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
     */
    private function registerConfigServices(): void
    {
        $configServices = $this->config->di_container['services'] ?? [];

        foreach ($configServices as $id => $name) {
            $this->set($id, $name);
        };
    }

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

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    public function set(string $id, string $name): void
    {
        if (!class_exists($name)) {
            throw new ServiceNotFoundException(
                "Service '{$id}' can't be registered - corresponding class not found: " . $name
            );
        }
        
        $this->services[$id] = $name;
    }

    private function callClass(string $name, array $params = []): mixed
    {
        if ($name === Container::class) {
            // Service is a container itself
            return $this;
        }
        
        if (array_key_exists($name, $this->instances)) {
            // Service is a singleton class and has already been instantiated. Return the instance.
            return $this->instances[$name];
        }
        
        $reflector = new ReflectionClass($name);
        
        $isSingleton = !!$reflector->getAttributes(Singleton::class);

        // Service can't be instantiated or '__constructor' method is not public
        if (!$reflector->isInstantiable()) {

            // Service is a singleton class and has not been instantiated yet
            if ($reflector->hasMethod('getInstance')) {
                $isSingleton = true;
                
                $constructor = $reflector->getMethod('getInstance');
            }
        }

        $constructor = $constructor ?? $reflector->getConstructor();

        // Service has no '__constructor' and no 'getInstance' method. Just instantiate it.
        if (
            is_null($constructor) 
            && !$reflector->isAbstract()
            && !$reflector->isInterface()
            && !$reflector->isTrait()
        ) {
            return $reflector->newInstance();
        }

        $dependencies = $this->resolveMethodDependencies($constructor, $name, $params);

        // Only get a new instance from reflector if the standard constructor is used,
        // otherwise, it's most likely a singleton with static 'getInstance' method.
        $newInstance = $constructor->getName() !== '__construct' ?
            null : $reflector->newInstanceWithoutConstructor();

        $constructor->invokeArgs($newInstance, $dependencies);

        // Service is a singleton. Save its instance.
        if ($isSingleton) {
            $this->instances[$name] = $newInstance;
        }

        return $newInstance;
    }

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

    private function resolveMethodDependencies(
        ReflectionMethod $method, 
        string $name, 
        array $presetParams = []
    ): array
    {
        $presetParamsAreNamed = count(array_filter(array_keys($presetParams), 'is_string')) > 0;

        $params = $method->getParameters();

        $dependencies = [];

        for ($i = 0; $i < count($params); $i++) {
            $param = $params[$i];
            $paramName = $param->getName();

            // Preset params are named. Get them by name.
            if  ($presetParamsAreNamed) {
                if (array_key_exists($paramName, $presetParams)) {
                    $dependencies[$paramName] = $presetParams[$paramName];
                    continue;
                }
            } else {
                // Preset params are not named. Use them in order.
                if (array_key_exists($i, $presetParams)) {
                    $dependencies[$paramName] = $presetParams[$i];
                    continue;
                }
            }

            // Dependency has default value. Use it.
            if ($param->isDefaultValueAvailable()) {
                $dependencies[$paramName] = $param->getDefaultValue();
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
                // Dependency is a primitive type. Throw an exception.
                if ($paramType->isBuiltin()) {
                    throw new ContainerException(
                        "'{$name}' service can't be resolved because '{$paramName}' param is not optional and has no default value"
                    );
                } else {
                    // Dependency is a class. Resolve it.
                    $dependencies[$paramName] = $this->get($paramType->getName());
                }
            }
        }
        
        return $dependencies;
    }
}