<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Template\TemplateInterface;

interface ControllerInterface
{
    public function render(TemplateInterface $template, array $params): void;
}