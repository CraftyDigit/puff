<?php

namespace CraftyDigit\Puff\Template;

use CraftyDigit\Puff\Common\Enums\TemplateEngine;

interface TemplateEngineManagerInterface
{
    public function getTemplateEngine(TemplateEngine $templateEngine): TemplateEngineInterface;
}