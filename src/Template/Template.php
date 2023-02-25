<?php

namespace CraftyDigit\Puff\Template;

class Template implements TemplateInterface
{
    /**
     * @var string
     */
    protected string $fullName;

    /**
     * @var string
     */
    protected string $path;

    /**
     * @param string $name
     * @param bool $isAdminTemplate
     */
    public function __construct(protected string $name, protected bool $isAdminTemplate = false)
    {
        $innerDirectory = $this->isAdminTemplate ? 'Admin' : 'Front';
        $this->fullName = $innerDirectory .'/'. $name;
        $this->path = $this->getTemplatesRootDirectory() . $this->fullName . '.php';
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
     */
    protected function getTemplatesRootDirectory(): string
    {
        return dirname(__DIR__) . '/Templates/';
    }
}