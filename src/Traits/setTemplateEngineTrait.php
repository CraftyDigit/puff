<?php

namespace CraftyDigit\Puff\Traits;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Enums\TemplateEngine;
use CraftyDigit\Puff\Template\TemplateEngineInterface;
use CraftyDigit\Puff\Template\TemplateEngineManagerInterface;
use Exception;

trait setTemplateEngineTrait
{
    private function setTemplateEngine(
        Config $config,
        TemplateEngineManagerInterface $templateEngineManager,
        ?TemplateEngineInterface &$templateEngine,        
    ): void
    {
        $defaultEngine = TemplateEngine::tryFrom($config->default_template_engine);

        if ($defaultEngine === null) {
            throw new Exception('Invalid default template engine');
        }

        $templateEngine = $templateEngineManager->getTemplateEngine($defaultEngine);
    }
}