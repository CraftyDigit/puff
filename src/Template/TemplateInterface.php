<?php

namespace CraftyDigit\Puff\Template;

interface TemplateInterface
{
    public function getName(): string;

    public function getPath(): string;

    public function checkIfFileExists(): bool;
}