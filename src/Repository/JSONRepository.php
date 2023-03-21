<?php

namespace CraftyDigit\Puff\Repository;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\Exceptions\FileNotFoundException;
use CraftyDigit\Puff\Helper;
use CraftyDigit\Puff\Model\Model;
use CraftyDigit\Puff\Model\ModelInterface;
use Exception;

class JSONRepository extends AbstractRepository implements RepositoryInterface 
{
    /**
     * @param ContainerExtendedInterface $container
     * @param Helper $helper
     * @param string $dataSourceName
     * @param array $data
     * @param bool $autoSave
     * @throws FileNotFoundException
     */
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

    /**
     * @return array
     */
    public function getAll(): array
    {
        $items = [];

        foreach ($this->data['items'] as $dataItem) {
            $items[] = $this->container->get(ModelInterface::class, ['data' => $dataItem]);
        }

        return $items;
    }

    /**
     * @param int $itemId
     * @return Model|null
     */
    public function getOneById(int $itemId): ?Model
    {
        foreach ($this->data['items'] as $dataItem) {
            if ($dataItem['id'] == $itemId) {
                return $this->container->get(ModelInterface::class, ['data' => $dataItem]);
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getScheme(): array
    {
        return array_keys($this->data['items'][0]);
    }

    /**
     * @return Model
     */
    public function getBlankItem(): Model
    {
        $scheme = $this->getScheme();

        $dataItem = [];

        foreach ($scheme as $field) {
            $dataItem[$field] = '';
        }

        return $this->container->get(ModelInterface::class, ['data' => $dataItem]);
    }

    /**
     * @param Model $item
     * @return void
     * @throws Exception
     */
    public function updateItem(Model $item): void
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

    /**
     * @param Model $item
     * @return Model
     * @throws Exception
     */
    public function addItem(Model $item): Model
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

        return $this->container->get(ModelInterface::class, ['data' => $newItemData]);
    }

    /**
     * @param Model $item
     * @return void
     * @throws Exception
     */
    public function deleteItem(Model $item): void
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

    /**
     * @return void
     * @throws Exception
     */
    public function saveData(): void
    {
        $file = $this->getDataFileFullName();

        file_put_contents($file, json_encode($this->data));
    }

    /**
     * @return void
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function loadData(): void
    {
        $file = $this->getDataFileFullName();

        if (!file_exists($file)) {
            throw new FileNotFoundException("JSON data file '$file' does not exist.");
        }
        
        $this->data = json_decode(file_get_contents($file), 1);
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getDataFileFullName(): string
    {
        return $this->helper->getPathToAppFile('Data/' . $this->dataSourceName . '.json'); 
    }
}