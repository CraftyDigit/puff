<?php

namespace CraftyDigit\Puff\Template;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Common\Enums\TemplateEngine;
use CraftyDigit\Puff\Common\Exceptions\ConfigParamException;

class TemplateEngineManager implements TemplateEngineManagerInterface
{
    public function __construct(
        private readonly ContainerExtendedInterface $container,
        private readonly Config $config,
    )
    {}
    
    public function getTemplateEngine(?TemplateEngine $templateEngine = null): TemplateEngineInterface
    {
        $templateEngine = $templateEngine ?? TemplateEngine::tryFrom($this->config->default_template_engine);

        if ($templateEngine === null) {
            throw new ConfigParamException('default_template_engine');
        }
        
        return match ($templateEngine) {
            TemplateEngine::PUFF => $this->container->get(PuffTemplateEngine::class),
            TemplateEngine::TWIG => $this->container->get(TwigTemplateEngine::class),
        };
    }
}