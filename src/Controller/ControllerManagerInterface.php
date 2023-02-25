<?php

namespace CraftyDigit\Puff\Controller;

interface ControllerManagerInterface
{
    /**
     * @param string $name
     * @param bool $isAdmin
     * @param string $relatedPath
     * @return ControllerInterface
     */
    public function getController(
        string $name, bool $isAdmin = false, string $relatedPath = '/'
    ): ControllerInterface;
}