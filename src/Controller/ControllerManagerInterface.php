<?php

namespace CraftyDigit\Puff\Controller;

interface ControllerManagerInterface
{
    /**
     * @param string $name
     * @return ControllerInterface
     */
    public function getController(string $name): ControllerInterface;
}