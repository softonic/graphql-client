<?php

namespace Softonic\GraphQL;

use Softonic\GraphQL\Query\Collection;
use Softonic\GraphQL\Query\Item;

class DataObjectBuilder
{
    public function build(array $data): array
    {
        $dataObject = [];
        foreach ($data as $queryName => $objectsData) {
            if (is_array($objectsData) && !empty($objectsData)) {
                if ($this->isAList($objectsData)) {
                    $items = [];
                    foreach ($objectsData as $objectData) {
                        $items[] = $this->buildItem($objectData);
                    }

                    $dataObject[$queryName] = new Collection($items);
                } else {
                    $dataObject[$queryName] = $this->buildItem($objectsData);
                }
            } else {
                $dataObject[$queryName] = $objectsData;
            }
        }

        return $dataObject;
    }

    private function isAList(array $data): bool
    {
        return array_values($data) === $data;
    }

    private function buildItem(array $data): Item
    {
        $itemData = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $items = [];
                foreach ($value as $valueItemData) {
                    $items[] = $this->buildItem($valueItemData);
                }

                $itemData[$key] = new Collection($items);
            } else {
                $itemData[$key] = $value;
            }
        }

        return new Item($itemData);
    }
}
