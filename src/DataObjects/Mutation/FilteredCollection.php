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

    /**
     * @TODO: TEST remove leaf lvl3 o lvl4 from root.
     */
    public function remove(Item $item): bool
    {
        foreach ($this->arguments as $key => $argument) {
            if ($argument instanceof Collection) {
                if ($argument->remove($item)) {
                    return true;
                }
            } elseif ($argument === $item) {
//                if (self instanceof FilteredCollection) {
//                    throw new \Exception('no se puedeee, solo de hijos. Añadir test.');
//                }
                unset($this->arguments[$key]);

                return true;
            }
        }

        return false;
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
