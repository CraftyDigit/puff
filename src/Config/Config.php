<?php

namespace CraftyDigit\Puff\Config;

use CraftyDigit\Puff\Attributes\Singleton;
use CraftyDigit\Puff\Helper;

#[Singleton]
class Config
{
    public function __construct(
        private readonly Helper $helper,
        private array $parameters = []
    )
    {
        $this->loadConfig();
    }
    
    public function __get($name)
    {
        return $this->parameters[$name] ?? null;
    }
    
    private function loadConfig(): void
    {
        $defaultConfigFile = dirname(__FILE__) . '/puff_config.json';
        
        $this->loadParametersFromFile($defaultConfigFile);
    }

    private function loadParametersFromFile(string $fileFullName): void
    {
        if (!file_exists($fileFullName)) {
            return;
        }

        $parameters = json_decode(file_get_contents($fileFullName), 1) ?? [];

        $this->parameters = $parameters + $this->parameters;

        $additionalConfigFiles = $parameters['additional_config_files'] ?? [];
        
        foreach ($additionalConfigFiles as $additionalConfigFileName) {
            $additionalConfigFileFullName = $this->helper->getPathToSrcFile(
                $additionalConfigFileName . '.json', true
            );
            
            $this->loadParametersFromFile($additionalConfigFileFullName);
        }
    }
}