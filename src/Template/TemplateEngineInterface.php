<?php

namespace CraftyDigit\Puff\Template;

interface TemplateEngineInterface
{
    public function render(string $templateName, array $data = []): string;
}