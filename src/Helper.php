<?php

namespace CraftyDigit\Puff;

use Composer\Autoload\ClassLoader;
use CraftyDigit\Puff\Common\Exceptions\FileSystemException;

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
            
        throw new FileSystemException(
            'Root folder not found. Please check your composer.json file. It should contain "App" namespace.'
        );    
    }
    
    public function getPuffRootDirectory(bool $allowFalse = false): string | false
    {
        $loaders = ClassLoader::getRegisteredLoaders();

        foreach ($loaders as $loader) {
            $prefixes = $loader->getPrefixesPsr4();

            foreach ($prefixes as $name => $path) {
                if ($name === 'CraftyDigit\\Puff\\') {
                    return $path[0];
                }
            }
        }
        
        if ($allowFalse) {
            return false;
        }
            
        throw new FileSystemException(
            'Root folder not found. Please check your composer.json file. It should contain "CraftyDigit\\Puff" namespace.'
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
    
    public function getPathToPuffSubDirectory(string $directoryName, bool $allowFalse = false): string | false
    {
        $puffRootDirectory = $this->getPuffRootDirectory($allowFalse);
        
        if ($puffRootDirectory === false) {
            return false;
        }

        return $puffRootDirectory . DIRECTORY_SEPARATOR . $directoryName;
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
    
    public function getSrcDirectoryFiles($directoryName): array
    {
        $files = [];
        
        $directoryPath = $this->getPathToSrcSubDirectory($directoryName, true);
        
        if ($directoryPath === false) {
            return $files;
        }
        
        $files = $this->getDirectoryFiles($directoryPath);
        
        return $files;
    }
    
    public function getPuffDirectoryFiles($directoryName): array
    {
        $files = [];
        
        $directoryPath = $this->getPathToPuffSubDirectory($directoryName, true);
        
        if ($directoryPath === false) {
            return $files;
        }
        
        $files = $this->getDirectoryFiles($directoryPath, false);
        
        return $files;
    }

    public function getDirectoryFiles(string $directoryPath, bool $isAppRoot = true): array
    {
        if (!is_dir($directoryPath)) {
            return [];
        }
        
        $ffs = scandir($directoryPath);

        if ($ffs === false) {
            return [];
        }

        $files = [];
        
        unset($ffs[array_search('.', $ffs, true)]);
        unset($ffs[array_search('..', $ffs, true)]);

        foreach($ffs as $ff){
            if(is_dir($directoryPath. DIRECTORY_SEPARATOR .$ff)) {
                $files = array_merge($files, $this->getDirectoryFiles($directoryPath. DIRECTORY_SEPARATOR .$ff, $isAppRoot));
            } else {
                $rootDirectory = $isAppRoot ? $this->getAppRootDirectory() : $this->getPuffRootDirectory();
                $directoryName = str_replace($rootDirectory, '', $directoryPath);
                
                $files[] = $directoryName . DIRECTORY_SEPARATOR . $ff;
            }
        }

        return $files;    
    }
}