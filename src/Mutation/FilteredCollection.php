<?php

namespace Softonic\GraphQL\Mutation;

use Softonic\GraphQL\Traits\JsonSerializer;

class FilteredCollection implements MutationObject, \JsonSerializable
{
    use JsonSerializer;

    protected $arguments = [];

    protected $config;

    private $hasChanged = false;

    public function __construct(array $arguments = [], array $config = [], bool $hasChanged = false)
    {
        $this->arguments  = $arguments;
        $this->config     = $config;
        $this->hasChanged = $hasChanged;
    }

    public function __get(string $key)
    {
        $items = [];

        foreach ($this->arguments as $argument) {

            $items[] = $argument->{$key};
        }

        return new Collection($items, $this->config[$key]->children);
    }

    public function set(array $data)
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof Collection) {
                $argument->set($data);
            } else {
                $this->setItemData($argument, $data);
            }
        }
    }

    public function filter(array $filters)
    {
        $filteredData = [];
        if (!empty($this->arguments[0]) && $this->arguments[0] instanceof Collection) {
            foreach ($this->arguments as $argument) {
                $argumentCopy = clone $argument;

                $filteredItems = $this->filterItems($argumentCopy->arguments, $filters);

                $argumentCopy->arguments = $filteredItems;

                $filteredData[] = $argumentCopy;
            }
        } else {
            $filteredItems = $this->filterItems($this->arguments, $filters);

            $filteredData = $filteredItems;
        }

        return new FilteredCollection($filteredData, $this->config);
    }

    public function hasChanged(): bool
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof MutationObject && $argument->hasChanged()) {
                $this->hasChanged = true;
            }
        }

        return $this->hasChanged;
    }

    public function jsonSerialize(): array
    {
        $items = [];
        foreach ($this->arguments as $item) {
            if ($item->hasChanged()) {
                if ($item instanceof \JsonSerializable) {
                    $items[] = $item->jsonSerialize();
                } else {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    private function setItemData(Item $item, array $data)
    {
        foreach ($data as $key => $value) {
            $item->{$key} = $value;
        }
    }

    private function filterItems(array $arguments, array $filters): array
    {
        return array_filter($arguments, function ($item) use ($filters) {
            foreach ($filters as $filterKey => $filterValue) {
                if (!$this->matchesFilter($item, $filterKey, $filterValue)) {
                    return false;
                }
            }

            return true;
        });
    }

    private function matchesFilter($item, string $filterKey, $filterValue): bool
    {
        return (is_null($filterValue) && is_null($item->{$filterKey})
            || !is_null($item->{$filterKey}) && $item->{$filterKey} == $filterValue);
    }
}
