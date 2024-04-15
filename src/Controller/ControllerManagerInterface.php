<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Common\Contracts\ResourceManagerInterface;

interface ControllerManagerInterface extends ResourceManagerInterface
{
    public function getController(string $name): AbstractController;
}