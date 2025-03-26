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

    protected function buildFilteredCollection($items): Collection
    {
        return new Collection($items);
    }

    protected function buildSubCollection(array $items, string $key): Collection
    {
        return new Collection($items);
    }
}
