<?php

namespace Softonic\GraphQL\DataObjects\Mutation;

use Softonic\GraphQL\DataObjects\AbstractCollection;
use Softonic\GraphQL\DataObjects\Mutation\Traits\MutationObjectHandler;

class FilteredCollection extends AbstractCollection implements MutationObject
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

    public function set(array $data): void
    {
        foreach ($this->arguments as $argument) {
            $argument->set($data);
        }
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

    protected function buildFilteredCollection($items)
    {
        return new FilteredCollection($items, $this->config);
    }

    protected function buildSubCollection(array $items, string $key)
    {
        return new Collection($items, $this->config[$key]->children);
    }
}
