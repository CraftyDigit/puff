<?php

namespace CraftyDigit\Puff\DataHandler\NoStruct;

use CraftyDigit\Puff\SimpleModel\SimpleModel;

interface NoStructRepositoryInterface
{
    public function findAll(): array;
    
    public function find(int $itemId): ?SimpleModel;
    
    public function findBy(
        array $criteria,
       ?array $orderBy = null,
       ?int $limit = null,
       ?int $offset = null
    ): array;
    
    public function findOneBy(array $criteria): ?SimpleModel;
    
    public function getScheme(): array;

    public function getBlankItem(): SimpleModel;

    public function updateItem(SimpleModel $item): void;

    public function addItem(SimpleModel $item): SimpleModel;

    public function deleteItem(SimpleModel $item): void;
    
    public function getDataSourceName(): string;
}