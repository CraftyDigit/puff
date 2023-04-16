<?php

namespace CraftyDigit\Puff;

use Composer\Autoload\ClassLoader;
use Exception;

class Helper
{
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

    public function getPathToAppSubDirectory(string $directoryName, bool $allowFalse = false): string | false
    {
        $appRootDirectory = $this->getAppRootDirectory($allowFalse);
        
        if ($appRootDirectory === false) {
            return false;
        }

        return $appRootDirectory . DIRECTORY_SEPARATOR . $directoryName;
    }
    
    public function getPathToSrcSubDirectory(string $directoryName, bool $allowFalse = false): string | false
    {
        return $this->getPathToAppSubDirectory('src' . DIRECTORY_SEPARATOR . $directoryName, $allowFalse); 
    }
    
    public function getPathToBuildSubDirectory(string $directoryName, bool $allowFalse = false): string | false
    {
        return $this->getPathToAppSubDirectory('build' . DIRECTORY_SEPARATOR . $directoryName, $allowFalse); 
    }

    public function getPathToAppFile(string $fileName, bool $allowFalse = false): string | false
    {
        $appRootDirectory = $this->getAppRootDirectory($allowFalse);
        
        if ($appRootDirectory === false) {
            return false;
        }

        return $appRootDirectory . DIRECTORY_SEPARATOR . $fileName;
    }
    
    public function getPathToSrcFile(string $fileName, bool $allowFalse = false): string | false
    {
        return $this->getPathToAppFile('src' . DIRECTORY_SEPARATOR . $fileName, $allowFalse); 
    }
    
    public function getPathToBuildFile(string $fileName, bool $allowFalse = false): string | false
    {
        return $this->getPathToAppFile('build' . DIRECTORY_SEPARATOR . $fileName, $allowFalse); 
    }

    public function getAppDirectoryFiles($directoryName): array
    {
        $files = [];
        
        $directoryPath = $this->getPathToAppSubDirectory($directoryName, true);
        
        if ($directoryPath === false) {
            return $files;
        }
        
        $files = $this->getDirectoryFiles($directoryPath);
        
        return $files;
    }

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
    
    public function sortArrayByFields(array $array, array $fields) : array
    {
        usort($array, function($a, $b) use ($fields) {
            foreach($fields as $field) {
                $valueA = $a[$field];
                $valueB = $b[$field];
                if ($valueA != $valueB) {
                    return ($valueA < $valueB) ? -1 : 1;
                }
            }
            return 0;
        });
        
        return $array;
    }
}