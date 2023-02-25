<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\ErrorReporter\ErrorCode;
use CraftyDigit\Puff\Exceptions\ClassNotFoundException;

class ControllerManager implements ControllerManagerInterface
{
    /**
     * @var Config 
     */
    public Config $config;

    public function __construct()
    {
        $this->config = Config::getInstance();
    }

    /**
     * @param string $name
     * @param bool $isAdmin
     * @param string $relatedPath
     * @return ControllerInterface
     * @throws ClassNotFoundException
     */
    public function getController(string $name, bool $isAdmin = false, string $relatedPath = '/'): ControllerInterface
    {
        $fullName = $this->constructFullName(...func_get_args());

        if ( class_exists($fullName) ) {
            /** @var ControllerInterface $controller */
            $controller = new $fullName();
        } else {
            if ($this->config->mode === 'dev') {
                throw new ClassNotFoundException($fullName);
            } else {
                $controller = $this->getErrorController(ErrorCode::Error404, $isAdmin);
            } 
        }

        return $controller;
    }

    /**
     * @param ErrorCode $errorCode
     * @param bool $isAdmin
     * @return ControllerInterface
     * @throws ClassNotFoundException
     */
    public function getErrorController(ErrorCode $errorCode, bool $isAdmin = false): ControllerInterface
    {
        $fullName = $this->constructFullName(name: $errorCode->name, isAdmin: $isAdmin);

        if (class_exists($fullName)) {
            $controller = new $fullName();
        } else {
            throw new ClassNotFoundException($fullName);
        }    
        
        return $controller;
    }

    /**
     * @param string $name
     * @param bool $isAdmin
     * @param string $relatedPath
     * @return string
     */
    private function constructFullName(string $name, bool $isAdmin = false, string $relatedPath = '/'): string 
    {
        $fullName = 'App';
        $fullName .= '\Controllers';
        $fullName .= $isAdmin ? '\Admin' : '\Front';
        $fullName .= $relatedPath === '/' ? '' : str_replace('/', '\\', $relatedPath);
        $fullName .= '\\' . $name . 'Controller';
        
        return $fullName;
    }
}