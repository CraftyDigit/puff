<?php

namespace CraftyDigit\Puff\Template;

use CraftyDigit\Puff\Helper;

class Template implements TemplateInterface
{
    public function __construct(
        private readonly string $name,
        private readonly Helper $helper,
        private string $path = ''
    )
    {
        $this->path = $this->getTemplatesDirectory() . DIRECTORY_SEPARATOR . $this->name . '.php';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function checkIfFileExists(): bool
    {
        return file_exists($this->path);
    }

    protected function getTemplatesDirectory(): string
    {
        return $this->helper->getPathToSrcSubDirectory('Templates');
    }
}