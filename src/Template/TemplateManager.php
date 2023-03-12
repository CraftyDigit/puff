<?php

namespace CraftyDigit\Puff\Template;

use Exception;

class TemplateManager implements TemplateManagerInterface
{
    /**
     * @param string $templateName
     * @return TemplateInterface
     * @throws Exception
     */
    public function getTemplate(string $templateName): TemplateInterface
    {
        return new Template($templateName);
    }
}