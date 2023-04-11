<?php

namespace CraftyDigit\Puff\DataHandler\NonORM;

use CraftyDigit\Puff\SimpleModel\SimpleModel;

interface NonORMRepositoryInterface
{
    public function getAll(): array;

    public function getOneById(int $itemId): ?SimpleModel;

    public function getScheme(): array;

    public function getBlankItem(): SimpleModel;

    public function updateItem(SimpleModel $item): void;

    public function addItem(SimpleModel $item): SimpleModel;

    public function deleteItem(SimpleModel $item): void;
}