<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Template\TemplateEngineInterface;
use CraftyDigit\Puff\Template\TemplateEngineManagerInterface;

abstract class AbstractPageController extends AbstractController
{
    public function __construct(
        protected ContainerExtendedInterface $container,
        protected readonly TemplateEngineManagerInterface $templateEngineManager,
        protected ?TemplateEngineInterface $templateEngine = null,
    )
    {
        $this->container->callMethod(parent::class, '__construct', target: $this);
        
        $this->templateEngine = $this->templateEngineManager->getTemplateEngine();
    }
}