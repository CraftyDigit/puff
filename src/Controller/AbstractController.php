<?php

namespace CraftyDigit\Puff\Controller;

use CraftyDigit\Puff\Config\Config;
use CraftyDigit\Puff\DataHandler\DataHandlerInterface;
use CraftyDigit\Puff\EntityManager\NoStruct\NoStructEntityManagerInterface;
use CraftyDigit\Puff\Enums\DataSourceType;
use CraftyDigit\Puff\Enums\TemplateEngine;
use CraftyDigit\Puff\Template\TemplateEngineInterface;
use CraftyDigit\Puff\Template\TemplateEngineManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

abstract class AbstractController
{
    public function __construct(
        protected readonly TemplateEngineManagerInterface $templateEngineManager,
        protected readonly DataHandlerInterface $dataHandler,
        protected readonly Config $config,
        protected ?TemplateEngineInterface $templateEngine = null,
        protected NoStructEntityManagerInterface|EntityManagerInterface|null $entityManager = null
    )
    {
        $this->setTemplateEngine();

        $this->setEntityManager();
    }

    private function setTemplateEngine(): void
    {
        $defaultEngine = TemplateEngine::tryFrom($this->config->default_template_engine);

        if ($defaultEngine === null) {
            throw new Exception('Invalid default template engine');
        }

        $this->templateEngine = $this->templateEngineManager->getTemplateEngine($defaultEngine);
    }

    private function setEntityManager(): void
    {
        $defaultEntityManager = DataSourceType::tryFrom($this->config->default_data_handler);

        if ($defaultEntityManager === null) {
            throw new Exception('Invalid default entity manager');
        }

        $this->entityManager = $this->dataHandler->getEntityManager($defaultEntityManager);
    }
}