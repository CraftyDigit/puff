<?php

namespace CraftyDigit\Puff\Template;

use CraftyDigit\Puff\Helper;
use Exception;

class Template implements TemplateInterface
{
    /**
     * @param string $name
     * @param string $path
     * @param Helper $helper
     * @throws Exception
     */
    public function __construct(
        private readonly string $name,
        private readonly Helper $helper,
        private string $path = ''
    )
    {
        $this->path = $this->getTemplatesDirectory() . DIRECTORY_SEPARATOR . $this->name . '.php';
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
        return $this->helper->getPathToAppDirectory('Templates');
    }
}