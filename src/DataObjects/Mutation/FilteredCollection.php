<?php

namespace Softonic\GraphQL\DataObjects\Mutation;

use Softonic\GraphQL\DataObjects\AbstractCollection;
use Softonic\GraphQL\DataObjects\Mutation\Traits\MutationObjectHandler;

class FilteredCollection extends AbstractCollection implements MutationObject, \IteratorAggregate, \JsonSerializable
{
    use MutationObjectHandler;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var bool
     */
    protected $hasChanged = false;

    public function __construct(array $arguments = [], array $config = [], bool $hasChanged = false)
    {
        parent::__construct($arguments);

        $this->config     = $config;
        $this->hasChanged = $hasChanged;
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

    public function hasItem(array $itemData): bool
    {
        foreach ($this->arguments as $argument) {
            $method = $argument instanceof FilteredCollection ? 'hasItem' : 'exists';

            if ($argument->$method($itemData)) {
                return true;
            }
        }

        return false;
    }


    public function __get(string $key): Collection
    {
        $items = [];
        foreach ($this->arguments as $argument) {
            $items[] = $argument->{$key};
        }

        return new Collection($items, $this->config[$key]->children);
    }

    public function set(array $data): void
    {
        foreach ($this->arguments as $argument) {
            $argument->set($data);
        }
    }

    public function getIterator(): \RecursiveIteratorIterator
    {
        return new \RecursiveIteratorIterator(new CollectionIterator($this->arguments));
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

    public function filter(array $filters): FilteredCollection
    {
        $filteredData = [];
        if ($this->areAllArgumentsCollections()) {
            foreach ($this->arguments as $argument) {
                $filteredData[] = $argument->filter($filters);
            }
        } else {
            $filteredData = $this->filterItems($this->arguments, $filters);
        }

        return new FilteredCollection($filteredData, $this->config);
    }

    private function areAllArgumentsCollections(): bool
    {
        return (!empty($this->arguments[0]) && $this->arguments[0] instanceof Collection);
    }

    private function filterItems(array $arguments, array $filters): array
    {
        $filteredItems = array_filter($arguments, function ($item) use ($filters) {
            foreach ($filters as $filterKey => $filterValue) {
                if (!($item->{$filterKey} == $filterValue)) {
                    return false;
                }
            }

            return true;
        });

        return array_values($filteredItems);
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

    public function __unset($key): void
    {
        foreach ($this->arguments as $argument) {
            unset($argument->{$key});
        }
    }

    public function remove(Item $item): void
    {
        foreach ($this->arguments as $key => $argument) {
            if ($argument instanceof Collection) {
                $argument->remove($item);
            } elseif ($argument === $item) {
                unset($this->arguments[$key]);
            }
        }
    }
}