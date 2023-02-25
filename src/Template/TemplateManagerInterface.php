<?php

namespace CraftyDigit\Puff\Template;

interface TemplateManagerInterface
{
    /**
     * @param string $templateName
     * @param bool $isAdminTemplate
     * @return TemplateInterface
     */
    public function getTemplate(string $templateName, bool $isAdminTemplate = false): TemplateInterface;
}