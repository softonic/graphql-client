<?php

namespace Softonic\GraphQL\Mutation;

use Softonic\GraphQL\Mutation\Traits\MutationObjectHandler;

class FilteredCollection implements MutationObject, \IteratorAggregate, \JsonSerializable
{
    use MutationObjectHandler;

    /**
     * @var array<Item>
     */
    protected $arguments = [];

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
        $this->arguments  = $arguments;
        $this->config     = $config;
        $this->hasChanged = $hasChanged;
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

    public function getIterator()
    {
        return new \RecursiveIteratorIterator($this->getRecursiveArrayIterator());
    }

    public function getRecursiveArrayIterator(): \RecursiveArrayIterator
    {
        return new class($this->arguments) extends \RecursiveArrayIterator {
            public function hasChildren(): bool
            {
                $current = $this->current();
                if ($current instanceof Item) {
                    return false;
                }

                if (is_array($current)) {
                    return true;
                }

                return $current->hasChildren();
            }

            public function getChildren()
            {
                return parent::current()->getRecursiveArrayIterator();
            }
        };
    }

    public function hasChildren()
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
                $filteredItems = $this->filterItems($argument->arguments, $filters);

                $filteredData[] = new FilteredCollection($filteredItems, $this->config);
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
        $items = [];
        foreach ($this->arguments as $item) {
            if ($item->hasChanged()) {
                $items[] = $item->jsonSerialize();
            }
        }

        return $items;
    }
}
