<?php

namespace CraftyDigit\Puff\Template;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Enums\TemplateEngine;

class TemplateEngineManager implements TemplateEngineManagerInterface
{
    public function __construct(private readonly ContainerExtendedInterface $container)
    {}
    
    public function getTemplateEngine(TemplateEngine $templateEngine): TemplateEngineInterface
    {
        return match ($templateEngine) {
            TemplateEngine::PUFF => $this->container->get(PuffTemplateEngine::class),
            TemplateEngine::TWIG => $this->container->get(TwigTemplateEngine::class),
        };
    }
}