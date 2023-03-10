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
    public function getPathToAppDirectory(string $directoryName, bool $allowFalse = false): string | false
    {
        $appRootDirectory = $this->getAppRootDirectory($allowFalse);
        
        if ($appRootDirectory === false) {
            return false;
        }

        return $appRootDirectory . DIRECTORY_SEPARATOR . $directoryName;
    }

    /**
     * @param string $fileName
     * @param bool $allowFalse
     * @return string|false
     * @throws Exception
     */
    public function getPathToAppFile(string $fileName, bool $allowFalse = false): string | false
    {
        $appRootDirectory = $this->getAppRootDirectory($allowFalse);
        
        if ($appRootDirectory === false) {
            return false;
        }

        return $appRootDirectory . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * @param $directoryName
     * @return array
     * @throws Exception
     */
    public function getAppDirectoryFiles($directoryName): array
    {
        $files = [];
        
        $directoryPath = $this->getPathToAppDirectory($directoryName, true);
        
        if ($directoryPath === false) {
            return $files;
        }
        
        $files = $this->getDirectoryFiles($directoryPath);
        
        return $files;
    }

    /**
     * @param $directoryPath
     * @return array
     */
    public function getDirectoryFiles($directoryPath): array
    {
        $files = [];

        $ffs = scandir($directoryPath);

        unset($ffs[array_search('.', $ffs, true)]);
        unset($ffs[array_search('..', $ffs, true)]);

        foreach($ffs as $ff){
            if(is_dir($directoryPath.'/'.$ff)) {
                $files = array_merge($files, $this->getDirectoryFiles($directoryPath.'/'.$ff));
            } else {
                $appRootDirectory = $this->getAppRootDirectory();
                $directoryName = str_replace($appRootDirectory, '', $directoryPath);
                
                $files[] = $directoryName . '/' . $ff;
            }
        }

        return $files;    
    }
}