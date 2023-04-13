<?php

namespace CraftyDigit\Puff\DataHandler\NoStruct\JSON;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\DataHandler\NoStruct\AbstractNoStructRepository;
use CraftyDigit\Puff\Exceptions\FileNotFoundException;
use CraftyDigit\Puff\Helper;
use CraftyDigit\Puff\SimpleModel\SimpleModel;
use CraftyDigit\Puff\SimpleModel\SimpleModelInterface;

class JSONRepository extends AbstractNoStructRepository 
{
    public function __construct(
        private readonly ContainerExtendedInterface $container,
        private readonly Helper                     $helper,
        string                                      $dataSourceName,
        private array                               $data = [],
        public bool                                 $autoSave = true
    )
    {
        $this->container->callMethod(parent::class, '__construct', ['dataSourceName' => $dataSourceName] ,$this);
        
        $this->loadData();
    }

    public function getAll(): array
    {
        $items = [];

        foreach ($this->data['items'] as $dataItem) {
            $items[] = $this->container->get(SimpleModelInterface::class, ['data' => $dataItem]);
        }

        return $items;
    }

    public function getOneById(int $itemId): ?SimpleModel
    {
        foreach ($this->data['items'] as $dataItem) {
            if ($dataItem['id'] == $itemId) {
                return $this->container->get(SimpleModelInterface::class, ['data' => $dataItem]);
            }
        }

        return null;
    }

    public function getScheme(): array
    {
        return array_keys($this->data['items'][0]);
    }

    public function getBlankItem(): SimpleModel
    {
        $scheme = $this->getScheme();

        $dataItem = [];

        foreach ($scheme as $field) {
            $dataItem[$field] = '';
        }

        return $this->container->get(SimpleModelInterface::class, ['data' => $dataItem]);
    }

    public function updateItem(SimpleModel $item): void
    {
        for ($i = 0; $i < sizeof($this->data['items']); $i++) {
            if ($this->data['items'][$i]['id'] == $item->id) {
                $this->data['items'][$i] = $item->getData();
            }
        }

        if ($this->autoSave) {
            $this->saveData();
        }
    }

    public function addItem(SimpleModel $item): SimpleModel
    {
        $maxId = 0;

        for ($i = 0; $i < sizeof($this->data['items']); $i++) {
            $maxId = max($maxId, $this->data['items'][$i]['id']);
        }

        $newItemData = $item->getData();
        $newItemData['id'] = $maxId + 1;

        $this->data['items'][] = $newItemData;

        if ($this->autoSave) {
            $this->saveData();
        }

        return $this->container->get(SimpleModelInterface::class, ['data' => $newItemData]);
    }

    public function deleteItem(SimpleModel $item): void
    {
        for ($i = 0; $i < sizeof($this->data['items']); $i++) {
            if ($this->data['items'][$i]['id'] == $item->id) {
                array_splice($this->data['items'], $i, 1);
            }
        }

        if ($this->autoSave) {
            $this->saveData();
        }
    }

    public function saveData(): void
    {
        $file = $this->getDataFileFullName();

        file_put_contents($file, json_encode($this->data));
    }

    public function loadData(): void
    {
        $file = $this->getDataFileFullName();

        if (!file_exists($file)) {
            throw new FileNotFoundException("JSON data file '$file' does not exist.");
        }
        
        $this->data = json_decode(file_get_contents($file), 1);
    }

    private function getDataFileFullName(): string
    {
        return $this->helper->getPathToAppFile('Data/json/' . $this->dataSourceName . '.json'); 
    }
}