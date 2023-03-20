<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Exceptions\FileNotFoundException;
use CraftyDigit\Puff\Template\TemplateInterface;
use Exception;

abstract class AbstractController implements ControllerInterface
{
    /**
     * @param ContainerExtendedInterface $container
     * @param TemplateInterface|null $template
     */
    public function __construct(
        protected ContainerExtendedInterface $container,
        public ?TemplateInterface $template = null
    )
    {
        $this->template = $this->template ??
            $this->container->get(TemplateInterface::class, ['name' => $this->getDefaultTemplateName()]);
    }

    /**
     * @return string
     */
    public function getDefaultTemplateName(): string
    {
        $classFullName = get_class($this);

        $templateName = str_replace('App\\Controllers\\','', $classFullName);
        $templateName = str_replace('Controller', '', $templateName);
        $templateName = str_replace('\\', DIRECTORY_SEPARATOR, $templateName);
        
        $templateNameArr = explode(DIRECTORY_SEPARATOR, $templateName);
        $templateNameArr[sizeof($templateNameArr) - 1] = strtolower($templateNameArr[sizeof($templateNameArr) - 1]);

        return implode(DIRECTORY_SEPARATOR, $templateNameArr);
    }

    /**
     * @param array $variables
     * @return void
     * @throws Exception
     */
    protected function output(array $variables = []): void
    {
        $templatePath = $this->template->getPath();
        $output = '';
        
        if (!$this->template->checkIfFileExists()) {
            throw new FileNotFoundException("Template file '$templatePath' is not exist!");
        }

        extract($variables);

        ob_start();

        include $templatePath;

        $output = ob_get_clean();

        print $output;
    }

    /**
     * @param TemplateInterface|null $template
     * @param array $params
     * @return void
     * @throws Exception
     */
    public function render(?TemplateInterface $template = null, array $params = []): void
    {
        if ($template) {
            $this->template = $template;
        }
        
        $this->output($params);
    }
    
}