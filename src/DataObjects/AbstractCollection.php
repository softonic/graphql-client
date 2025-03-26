<?php

namespace Softonic\GraphQL\DataObjects;

use ArrayAccess;
use IteratorAggregate;
use RecursiveIteratorIterator;
use Softonic\GraphQL\DataObjects\Mutation\MutationObject;
use Softonic\GraphQL\DataObjects\Query\Collection;
use Softonic\GraphQL\Exceptions\InaccessibleArgumentException;
use Softonic\GraphQL\Traits\CollectionArrayAccess;

abstract class AbstractCollection extends AbstractObject implements IteratorAggregate, ArrayAccess
{
    use CollectionArrayAccess;

    public function has(string $key): bool
    {
        foreach ($this->arguments as $argument) {
            if ($argument->has($key)) {
                return true;
            }
        }

        return false;
    }

    public function getIterator(): RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(new CollectionIterator($this->arguments));
    }

    public function count(): int
    {
        $count = 0;
        foreach ($this->arguments as $argument) {
            if ($argument instanceof AbstractCollection) {
                $count += $argument->count();
            } elseif ($argument instanceof AbstractItem) {
                ++$count;
            }
        }

        return $count;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function __get(string $key): AbstractCollection
    {
        if ($this->arguments === []) {
            throw InaccessibleArgumentException::fromEmptyArguments($key);
        }

        $items = [];
        foreach ($this->arguments as $argument) {
            $arguments = $argument->{$key};
            if ($arguments instanceof Collection) {
                foreach ($arguments as $item) {
                    $items[] = $item;
                }
            } else {
                $items[] = $arguments;
            }
        }

        return $this->buildSubCollection($items, $key);
    }

    public function hasItem(array $itemData): bool
    {
        foreach ($this->arguments as $argument) {
            $method = $argument instanceof AbstractCollection ? 'hasItem' : 'equals';

            if ($argument->$method($itemData)) {
                return true;
            }
        }

        return false;
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

    public function filter(array $filters): AbstractCollection
    {
        $filteredData = [];
        if ($this->areAllArgumentsCollections()) {
            foreach ($this->arguments as $argument) {
                $data = $argument->filter($filters);
                if (!$data->isEmpty()) {
                    $filteredData[] = $data;
                }
            }
        } else {
            $filteredData = $this->filterItems($this->arguments, $filters);
        }

        return $this->buildFilteredCollection($filteredData);
    }

    private function areAllArgumentsCollections(): bool
    {
        return (!empty($this->arguments[0]) && $this->arguments[0] instanceof AbstractCollection);
    }

    private function filterItems(array $arguments, array $filters): array
    {
        $filteredItems = array_filter(
            $arguments,
            function ($item) use ($filters): bool {
                foreach ($filters as $filterKey => $filterValue) {
                    if ($item->{$filterKey} != $filterValue) {
                        return false;
                    }
                }

                return true;
            }
        );

        return array_values($filteredItems);
    }

    abstract protected function buildFilteredCollection($items);

    abstract protected function buildSubCollection(array $items, string $key);
}
