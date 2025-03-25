<?php

namespace Softonic\GraphQL\DataObjects\Query;

use Softonic\GraphQL\DataObjects\AbstractCollection;

class Collection extends AbstractCollection implements QueryObject
{
    public function jsonSerialize(): array
    {
        if (!$this->hasChildren()) {
            return [];
        }

        $items = [];
        foreach ($this->arguments as $item) {
            $items[] = $item->jsonSerialize();
        }

        return $items;
    }

    protected function buildFilteredCollection($items): \Softonic\GraphQL\DataObjects\Query\Collection
    {
        return new Collection($items);
    }

    protected function buildSubCollection(array $items, string $key): \Softonic\GraphQL\DataObjects\Query\Collection
    {
        return new Collection($items);
    }
}
