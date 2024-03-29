<?php

namespace CraftyDigit\Puff\Template;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\EventDispatcher\EventDispatcherHelperInterface;
use CraftyDigit\Puff\Helper;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class TwigTemplateEngine implements TemplateEngineInterface
{
    public function __construct(
        private readonly Helper $helper,
        private readonly Config $config,
        private readonly ContainerExtendedInterface $container,
        private readonly FilesystemLoader $loader,
        private readonly EventDispatcherHelperInterface $eventDispatcherHelper,
        private ?Environment $twig = null
    )
    {
        $templatePath = $this->helper->getPathToSrcSubDirectory('Templates');

        $this->loader->addPath($templatePath);
        
        $twigConfig = $this->config->twig;
        
        $cache = $twigConfig['cache'] ? $this->helper->getPathToAppSubDirectory($twigConfig['cache']) : false;
        
        if ($cache && !is_dir($cache)) {
            mkdir($cache, 0777, true);
        }
        
        $this->twig = $this->container->get(Environment::class, [
            'loader' => $this->loader,
            'options' => [
                'cache' => $cache,
                'debug' => $twigConfig['debug'],
            ]
        ]);

        if ($twigConfig['debug']) {
            $this->twig->addExtension(new DebugExtension());
        }
    }

    public function render(string $templateName, array $data = []): string
    {
        $templateName = $this->addExtToName($templateName);

        $data = (array) $this->eventDispatcherHelper->dispatchInNewEvent(
            (object) $data,
            'template.before_render'
        );

        return $this->twig->render($templateName, $data);
    }

    private function addExtToName(string $templateName): string
    {
        return $templateName . '.' . $this->config->twig['template_extension'];
    }
}
