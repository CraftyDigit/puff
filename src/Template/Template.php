<?php

namespace CraftyDigit\Puff\Template;

use CraftyDigit\Puff\Helper;
use Exception;

class Template implements TemplateInterface
{
    /**
     * @param string $name
     * @param string $path
     * @param string $fullName
     * @param bool $isAdminTemplate
     * @param Helper $helper
     * @throws Exception
     */
    public function __construct(
        protected string $name,
        protected string $path = '',
        protected string $fullName = '',
        protected readonly bool $isAdminTemplate = false,
        protected readonly Helper $helper = new Helper()
    )
    {
        $innerDirectory = $this->isAdminTemplate ? 'Admin' : 'Front';
        $this->fullName = $innerDirectory . DIRECTORY_SEPARATOR . $name;
        $this->path = $this->getTemplatesDirectory() . $this->fullName . '.php';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function checkIfFileExists(): bool
    {
        return file_exists($this->path);
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getTemplatesDirectory(): string
    {
        return $this->helper->getPathToDirectory('Templates');
    }
}