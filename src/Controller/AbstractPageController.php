<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Common\Enums\ResponseType;
use CraftyDigit\Puff\Template\TemplateEngineInterface;
use CraftyDigit\Puff\Template\TemplateEngineManagerInterface;
use Psr\Http\Message\RequestInterface;

abstract class AbstractPageController extends AbstractController
{
    public function __construct(
        protected ContainerExtendedInterface $container,
        protected readonly TemplateEngineManagerInterface $templateEngineManager,
        protected ?TemplateEngineInterface $templateEngine = null,
        protected ?RequestInterface $request = null,
        protected ResponseType $defaultResponseType = ResponseType::HTML,
    )
    {
        $this->container->callMethod(parent::class, '__construct', get_defined_vars(), $this);

        $this->templateEngine = $this->templateEngine ?? $this->templateEngineManager->getTemplateEngine();
    }
}