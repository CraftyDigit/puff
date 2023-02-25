<?php

namespace CraftyDigit\Puff\Template;

interface TemplateInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getFullName(): string;

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @return bool
     */
    public function checkIfFileExists(): bool;
}