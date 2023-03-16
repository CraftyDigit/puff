<?php

namespace CraftyDigit\Puff\Config;

use CraftyDigit\Puff\Helper;
use Exception;

class Config
{
    /**
     * @var Config|null
     */
    private static ?Config $instance = null;

    /**
     * @throws Exception
     */
    private function __construct(
        protected array $parameters = [],
        protected readonly Helper $helper = new Helper()
    )
    {
        $this->loadParameters();
    }

    /**
     * @return Config
     */
    public static function getInstance(): Config
    {
        if (self::$instance == null) {
            self::$instance = new Config();
        }

        return self::$instance;
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