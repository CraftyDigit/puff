<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Enums\TemplateEngine;
use CraftyDigit\Puff\Template\TemplateEngineInterface;
use CraftyDigit\Puff\Template\TemplateEngineManagerInterface;
use Exception;

abstract class AbstractController
{
    public function __construct(
        protected readonly TemplateEngineManagerInterface $templateEngineManager,
        protected readonly Config $config,
        protected ?TemplateEngineInterface $templateEngine = null
    )
    {
        $defaultEngine = TemplateEngine::tryFrom($this->config->default_template_engine);
        
        if ($defaultEngine === null) {
            throw new Exception('Invalid default template engine');
        }
        
        $this->templateEngine = $this->templateEngineManager->getTemplateEngine($defaultEngine);
    }
}