<?php

namespace CraftyDigit\Puff;

use Composer\Autoload\ClassLoader;
use Exception;

class Helper
{
    /**
     * @param bool $allowFalse
     * @return string|false
     * @throws Exception
     */
    public function getAppRootDirectory(bool $allowFalse = false): string | false
    {
        $loaders = ClassLoader::getRegisteredLoaders();

        foreach ($loaders as $loader) {
            $prefixes = $loader->getPrefixesPsr4();

            foreach ($prefixes as $name => $path) {
                if ($name === 'App\\') {
                    return $path[0];
                }
            }
        }
        
        if ($allowFalse) {
            return false;
        }
            
        throw new Exception(
            'Root folder not found. Please check your composer.json file. It should contain "App" namespace.'
        );    
    }

    /**
     * @param string $directoryName
     * @param bool $allowFalse
     * @return string|false
     * @throws Exception
     */
    public function getPathToDirectory(string $directoryName, bool $allowFalse = false): string | false
    {
        $appRootDirectory = $this->getAppRootDirectory($allowFalse);
        
        if ($appRootDirectory === false) {
            return false;
        }

        return $appRootDirectory . DIRECTORY_SEPARATOR . $directoryName . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $fileName
     * @param bool $allowFalse
     * @return string|false
     * @throws Exception
     */
    public function getPathToFile(string $fileName, bool $allowFalse = false): string | false
    {
        $appRootDirectory = $this->getAppRootDirectory($allowFalse);
        
        if ($appRootDirectory === false) {
            return false;
        }

        return $appRootDirectory . DIRECTORY_SEPARATOR . $fileName;
    }
}