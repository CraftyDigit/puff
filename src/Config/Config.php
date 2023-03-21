<?php

namespace CraftyDigit\Puff\Config;

use CraftyDigit\Puff\Attributes\Singleton;
use CraftyDigit\Puff\Helper;
use Exception;

#[Singleton]
class Config
{
    /**
     * @throws Exception
     */
    public function __construct(
        private readonly Helper $helper,
        private array $parameters = []
    )
    {
        $this->loadParameters();
    }
    
    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function loadParameters(): void
    {
        /* Custom config */
        $configFile = $this->helper->getPathToAppFile('puff_config.json', true);
        
        /* Default config */
        $defaultConfigFile = dirname(__FILE__) . '/puff_config.json';

        $this->parameters = file_exists($configFile) ? json_decode(file_get_contents($configFile), 1) : [];
        $this->parameters += json_decode(file_get_contents($defaultConfigFile), 1);
    }
}