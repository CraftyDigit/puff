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
        private array $parameters = [],
        private readonly Helper $helper = new Helper()
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
     * @return mixed
     */
    public function __get($name)
    {
        return $this->parameters[$name];
    }

    /**
     * @return void
     * @throws Exception
     */
    private function loadParameters(): void
    {
        $configFile = $this->helper->getPathToFile('puff_config.json', true);

        /* Default config */
        if ($configFile === false || file_exists($configFile) === false) {
            $configFile = dirname(__FILE__) . '/puff_config.json';
        }  

        $this->parameters = json_decode(file_get_contents($configFile), 1);
    }
}