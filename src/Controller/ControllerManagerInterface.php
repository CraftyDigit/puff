<?php

namespace CraftyDigit\Puff\Controller;

interface ControllerManagerInterface
{
    public function getController(string $name): AbstractController;
}