<?php

namespace CraftyDigit\Puff\EntityManager\NoSQL\JSON;

use CraftyDigit\Puff\Container\ContainerExtendedInterface;
use CraftyDigit\Puff\EntityManager\NoSQL\AbstractNoSQLRepository;
use CraftyDigit\Puff\Common\Exceptions\FileNotFoundException;
use CraftyDigit\Puff\Helper;
use CraftyDigit\Puff\Model\Model;
use CraftyDigit\Puff\Model\ModelInterface;
use Exception;

class JSONRepository extends AbstractNoSQLRepository
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

    public function findAll(): array
    {
        return $this->findBy(criteria: []);
    }

    public function find(int $itemId): ?Model
    {
        $items = $this->findBy(criteria: ['id' => $itemId], limit: 1);

        if (count($items) > 0) {
            return $items[0];
        }

        return null;
    }
    
    public function findOneBy(array $criteria): ?Model
    {
        $items = $this->findBy(criteria: $criteria, limit: 1);

        if (count($items) > 0) {
            return $items[0];
        }

        return null;
    }
    
    public function findBy(
        array $criteria = [], 
        ?array $orderBy = null, 
        ?int $limit = null, 
        ?int $offset = null): array
    {
        $items = [];
        
        foreach ($this->data['items'] as $dataItem) {
            $itemSuitable = true;
            
            foreach ($criteria as $field => $compareData) {
                if (!isset($dataItem[$field])) {
                    throw new Exception("Field $field not found in data source");
                }
                
                if (is_array($compareData)) {
                    list($value, $operator) = $compareData;
                } else {
                    $value = $compareData;
                    $operator = '=';
                }
                
                if ($operator == '=') {
                    if ($dataItem[$field] != $value) {
                        $itemSuitable = false;
                        
                        break;
                    }
                } else if ($operator == '!=') {
                    if ($dataItem[$field] == $value) {
                        $itemSuitable = false;
                        
                        break;
                    }
                } else if ($operator == '>') {
                    if ($dataItem[$field] <= $value) {
                        $itemSuitable = false;
                        
                        break;
                    }
                } else if ($operator == '>=') {
                    if ($dataItem[$field] < $value) {
                        $itemSuitable = false;
                        
                        break;
                    }
                } else if ($operator == '<') {
                    if ($dataItem[$field] >= $value) {
                        $itemSuitable = false;
                        
                        break;
                    }
                } else if ($operator == '<=') {
                    if ($dataItem[$field] > $value) {
                        $itemSuitable = false;
                        
                        break;
                    }
                } else if ($operator == 'in') {
                    if (!in_array($dataItem[$field], $value)) {
                        $itemSuitable = false;
                        
                        break;
                    }
                } else if ($operator == 'not in') {
                    if (in_array($dataItem[$field], $value)) {
                        $itemSuitable = false;
                        
                        break;
                    }
                } else if ($operator == 'like') {
                    if (strpos($dataItem[$field], $value) === false) {
                        $itemSuitable = false;
                        
                        break;
                    }
                } else if ($operator == 'not like') {
                    if (strpos($dataItem[$field], $value) !== false) {
                        $itemSuitable = false;
                        
                        break;
                    }
                } else if ($operator == 'is null') {
                    if ($dataItem[$field] !== null) {
                        $itemSuitable = false;
                        
                        break;
                    }
                } else if ($operator == 'is not null') {
                    if ($dataItem[$field] === null) {
                        $itemSuitable = false;
                        
                        break;
                    }
                } else {
                    throw new Exception("Operator $operator not supported");
                }
            }

            if ($itemSuitable) {
                if ($offset && $offset > 0) {
                    $offset--;
                    continue;
                }

                if (!is_null($limit)) {
                    if ($limit > 0) {
                        $limit--;
                    } else {
                        break;
                    }
                }
                
                $items[] = $dataItem;
            }
        }

        if ($orderBy) {
            $multisortParams = [];
            foreach ($orderBy as $field => $order) {
                $multisortParams[] = array_column($items, $field);
                $multisortParams[] = strtolower($order) === 'desc' ? SORT_DESC : SORT_ASC;
            }

            $multisortParams[] = &$items;

            array_multisort(...$multisortParams);
        }

        foreach ($items as $idx => $item) {
            $items[$idx] = $this->container->get(ModelInterface::class, ['data' => $item]);
        }
        
        return $items;
    }

    public function getScheme(): array
    {
        return array_keys($this->data['items'][0]);
    }

    public function getBlankItem(): Model
    {
        $scheme = $this->getScheme();

        $dataItem = [];

        foreach ($scheme as $field) {
            $dataItem[$field] = '';
        }

        return $this->container->get(ModelInterface::class, ['data' => $dataItem]);
    }

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

    public function getDataSourceName(): string
    {
        return $this->dataSourceName;
    }

    private function getDataFileFullName(): string
    {
        $ds = DIRECTORY_SEPARATOR;
        
        return $this->helper->getPathToSrcFile('Data' . $ds .'json'. $ds . $this->dataSourceName . '.json');
    }
}