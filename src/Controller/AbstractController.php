<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Exceptions\FileNotFoundException;
use CraftyDigit\Puff\Template\TemplateInterface;
use CraftyDigit\Puff\Template\TemplateManager;
use CraftyDigit\Puff\Template\TemplateManagerInterface;
use Exception;

abstract class AbstractController implements ControllerInterface
{
    /**
     * @param TemplateInterface|null $template
     * @param TemplateManagerInterface $templateManager
     * @throws Exception
     */
    public function __construct(
        public ?TemplateInterface $template = null,
        protected readonly TemplateManagerInterface $templateManager = new TemplateManager()
    )
    {
        $this->template = $this->templateManager->getTemplate($this->getDefaultTemplateName());
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
        
        $templateName = implode(DIRECTORY_SEPARATOR, $templateNameArr);
        
        return $templateName;
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

        if($this->template->checkIfFileExists()){
            extract($variables);

            ob_start();

            include $templatePath;

            $output = ob_get_clean();
        } else {
            throw new FileNotFoundException("Template file '$templatePath' is not exist!");
        }

        print $output;
    }

    /**
     * @param TemplateInterface|null $template
     * @param array $params
     * @return void
     * @throws Exception
     */
    public function render(TemplateInterface $template = null, array $params = []): void
    {
        if ($template) {
            $this->template = $template;
        }
        
        $this->output($params);
    }
    
}