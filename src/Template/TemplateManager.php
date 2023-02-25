<?php

namespace CraftyDigit\Puff\Template;

class TemplateManager implements TemplateManagerInterface
{
    /**
     * @param string $templateName
     * @param bool $isAdminTemplate
     * @return TemplateInterface
     */
    public function getTemplate(string $templateName, bool $isAdminTemplate = false): TemplateInterface
    {
        return new Template($templateName, $isAdminTemplate);
    }
}