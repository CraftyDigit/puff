<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Template\TemplateEngineInterface;
use CraftyDigit\Puff\Template\TemplateEngineManagerInterface;
use CraftyDigit\Puff\Traits\PageControllerTrait;

abstract class AbstractPageController extends AbstractController
{
    use PageControllerTrait;
    
    public function __construct(
        protected ContainerExtendedInterface $container,
        protected readonly TemplateEngineManagerInterface $templateEngineManager,
        protected ?TemplateEngineInterface $templateEngine = null,
    )
    {
        $this->container->callMethod(parent::class, '__construct', target:  $this);
        
        $this->setTemplateEngine(
            $this->config,
            $this->templateEngineManager,
            $this->templateEngine,
        );
    }
}