<?php

namespace Softonic\GraphQL\DataObjects\Query;

use JsonSerializable;
use Softonic\GraphQL\DataObjects\AbstractCollection;
use Softonic\GraphQL\DataObjects\Interfaces\DataObject;

class Collection extends AbstractCollection implements QueryObject
{
    public function has(string $key): bool
    {
        foreach ($this->arguments as $argument) {
            if ($argument->has($key)) {
                return true;
            }
        }

        return false;
    }

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

    public function hasChildren(): bool
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof DataObject) {
                return true;
            }
        }

        return false;
    }

    public function toArray(): array
    {
        $item = [];
        foreach ($this->arguments as $key => $value) {
            $item[$key] = $value instanceof JsonSerializable ? $value->toArray() : $value;
        }

        return $item;
    }

    protected function buildFilteredCollection($items)
    {
        return new Collection($items);
    }

    protected function buildSubCollection(array $items, string $key)
    {
        return new Collection($items);
    }
}
