<?php

namespace CraftyDigit\Puff\EntityManager\NoSQL;

use CraftyDigit\Puff\Model\Model;

interface NoSQLRepositoryInterface
{
    public function findAll(): array;
    
    public function find(int $itemId): ?Model;
    
    public function findBy(
        array $criteria,
       ?array $orderBy = null,
       ?int $limit = null,
       ?int $offset = null
    ): array;
    
    public function findOneBy(array $criteria): ?Model;
    
    public function getScheme(): array;

    public function getBlankItem(): Model;

    public function updateItem(Model $item): void;

    public function addItem(Model $item): Model;

    public function deleteItem(Model $item): void;
    
    public function getDataSourceName(): string;
}