<?php

namespace CraftyDigit\Puff\Template;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Exceptions\FileNotFoundException;

class PuffTemplateEngine implements TemplateEngineInterface
{
    public function __construct(
        private readonly ContainerExtendedInterface $container,
        private readonly Config $config,
    )
    {}

    public function render(string $templateName, array $data = []): string
    {
        $template = $this->container->get(TemplateInterface::class, ['name' => $templateName]);

        $templatePath = $template->getPath();
        $output = '';

        if (!$template->checkIfFileExists()) {
            throw new FileNotFoundException("Template file '$templatePath' is not exist!");
        }

        extract($data);

        ob_start();

        include $templatePath;

        return ob_get_clean() ?: '';
    }

    private function addExtToName(string $templateName): string
    {
        return $templateName . '.' . $this->config->template_extension;
    }
}