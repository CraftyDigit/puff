<?php

namespace CraftyDigit\Puff\Template;

interface TemplateManagerInterface
{
    /**
     * @param string $templateName
     * @return TemplateInterface
     */
    public function getTemplate(string $templateName): TemplateInterface;
}