<?php

namespace CraftyDigit\Puff\Template;

use Exception;

class TemplateManager implements TemplateManagerInterface
{
    /**
     * @param string $templateName
     * @param bool $isAdminTemplate
     * @return TemplateInterface
     * @throws Exception
     */
    public function getTemplate(string $templateName, bool $isAdminTemplate = false): TemplateInterface
    {
        return new Template(name: $templateName, isAdminTemplate: $isAdminTemplate);
    }
}