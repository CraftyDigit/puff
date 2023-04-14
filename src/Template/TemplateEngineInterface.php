<?php

namespace CraftyDigit\Puff\Template;

interface TemplateEngineInterface
{
    public function render(string $templateName, array $data = []): string;
    
    public function display(string $templateName, array $data = []): void;
}