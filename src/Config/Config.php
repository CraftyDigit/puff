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
        private readonly Helper $helper,
        private array $parameters = []
    )
    {
        $this->loadParameters();
    }

    /**
     * @param Helper $helper
     * @param array $parameters
     * @return Config
     * @throws Exception
     */
    public static function getInstance(
        Helper $helper,
        array $parameters = []
    ): Config
    {
        if (self::$instance == null) {
            self::$instance = new Config(...func_get_args());
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