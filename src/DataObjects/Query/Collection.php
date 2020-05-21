<?php

namespace Softonic\GraphQL\DataObjects\Query;

use Softonic\GraphQL\DataObjects\AbstractCollection;
use Softonic\GraphQL\DataObjects\Interfaces\DataObject;
use Softonic\GraphQL\Exceptions\InaccessibleArgumentException;
use Softonic\GraphQL\Traits\GqlIterator;

class Collection extends AbstractCollection implements QueryObject
{
    use GqlIterator;

    public function __get(string $key): Collection
    {
        if (empty($this->arguments)) {
            throw InaccessibleArgumentException::fromEmptyArguments($key);
        }

        $items = [];
        foreach ($this->arguments as $argument) {
            $items[] = $argument->{$key};
        }

        return new Collection($items);
    }

    public function has(string $key): bool
    {
        foreach ($this->arguments as $argument) {
            if ($argument->has($key)) {
                return true;
            }
        }

        return false;
    }

    protected function buildFilteredCollection($data)
    {
        return new Collection($data);
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
            $item[$key] = $value instanceof \JsonSerializable ? $value->toArray() : $value;
        }

        return $item;
    }
}
