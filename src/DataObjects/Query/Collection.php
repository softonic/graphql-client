<?php

namespace Softonic\GraphQL\DataObjects\Query;

use Softonic\GraphQL\DataObjects\AbstractCollection;
use Softonic\GraphQL\DataObjects\Mutation\MutationObject;
use Softonic\GraphQL\Exceptions\InaccessibleArgumentException;
use Softonic\GraphQL\Traits\GqlIterator;

class Collection extends AbstractCollection implements QueryObject
{
    use GqlIterator;

    public function filter(array $filters): Collection
    {
        $filteredItems = array_filter($this->arguments, function ($item) use ($filters) {
            foreach ($filters as $filterKey => $filterValue) {
                if (!($item->{$filterKey} == $filterValue)) {
                    return false;
                }
            }

            return true;
        });

        return new Collection(array_values($filteredItems));
    }

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

    public function jsonSerialize(): array
    {
        if (!$this->hasChildren()) {
            return [];
        }

        $items = [];
        foreach ($this->arguments as $item) {
            if ($item->hasChanged()) {
                $items[] = $item->jsonSerialize();
            }
        }

        return $items;
    }

    public function hasChildren(): bool
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof MutationObject) {
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
