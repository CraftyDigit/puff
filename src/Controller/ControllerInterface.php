<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Template\TemplateInterface;

interface ControllerInterface
{
    /**
     * @param TemplateInterface $template
     * @param array $params
     * @return void
     */
    public function render(TemplateInterface $template, array $params): void;
}