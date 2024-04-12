<?php

namespace CraftyDigit\Puff\Config;

use CraftyDigit\Puff\Common\Attributes\Singleton;
use CraftyDigit\Puff\Helper;

#[Singleton]
class Config
{
    public function __construct(
        private readonly Helper $helper,
        private array $parameters = [],
        private array $defaultParameters = []
    )
    {
        $this->loadConfig();
    }

    public function __get(string $name): mixed
    {
        return $this->parameters[$name] ?? ($this->defaultParameters[$name] ?? null);
    }

    public function __set(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }

    public function getDefault(string $name): mixed
    {
        return $this->defaultParameters[$name] ?? null;
    }

    private function loadConfig(): void
    {
        $defaultConfigFile = dirname(__FILE__) . '/puff_config.json';
        
        $this->loadConfigFromFile($defaultConfigFile);
    }

    private function loadConfigFromFile(string $fileFullName): void
    {
        if (!file_exists($fileFullName)) {
            return;
        }

        $parameters = json_decode(file_get_contents($fileFullName), 1) ?? [];

        $this->defaultParameters = $parameters + $this->defaultParameters;

        $additionalConfigFiles = $parameters['additional_config_files'] ?? [];
        
        foreach ($additionalConfigFiles as $additionalConfigFileName) {
            $additionalConfigFileFullName = $this->helper->getPathToSrcFile(
                $additionalConfigFileName . '.json', true
            );
            
            $this->loadConfigFromFile($additionalConfigFileFullName);
        }
    }
}