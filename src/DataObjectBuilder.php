<?php

namespace Softonic\GraphQL;

use Softonic\GraphQL\Mutation\Collection as MutationCollection;
use Softonic\GraphQL\Mutation\Item as MutationItem;
use Softonic\GraphQL\Query\Collection as QueryCollection;
use Softonic\GraphQL\Query\Item as QueryItem;

class DataObjectBuilder
{
    const ITEM = 'item';

    const COLLECTION = 'collection';

    const QUERY_OBJECTS = [
        self::ITEM       => QueryItem::class,
        self::COLLECTION => QueryCollection::class,
    ];

    const MUTATION_OBJECTS = [
        self::ITEM       => MutationItem::class,
        self::COLLECTION => MutationCollection::class,
    ];

    public function buildQuery(array $data): array
    {
        return $this->build($data, self::QUERY_OBJECTS);
    }

    public function buildMutation(array $data): array
    {
        return $this->build($data, self::MUTATION_OBJECTS);
    }

    private function build(array $data, array $objects): array
    {
        $dataObject = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($this->isAList($value)) {
                    if (empty($value) || is_array($value[0])) {
                        $items = [];
                        foreach ($value as $objectData) {
                            $itemData = $this->build($objectData, $objects);
                            $items[]  = (new $objects[self::ITEM]($itemData));
                        }

                        $dataObject[$key] = (new $objects[self::COLLECTION]($items));
                    } else {
                        $dataObject[$key] = $value;
                    }
                } else {
                    $itemData         = $this->build($value, $objects);
                    $dataObject[$key] = (new $objects[self::ITEM]($itemData));
                }
            } else {
                $dataObject[$key] = $value;
            }
        }

        return $dataObject;
    }

    private function isAList(array $data): bool
    {
        return array_values($data) === $data;
    }
}
