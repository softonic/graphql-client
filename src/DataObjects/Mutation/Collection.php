<?php

namespace Softonic\GraphQL\DataObjects\Mutation;

class Collection extends FilteredCollection
{
    public function add(array $itemData): MutationObject
    {
        if (!empty($this->arguments[0]) && $this->arguments[0] instanceof Collection) {
            $elements = [];
            foreach ($this->arguments as $argument) {
                $elements[] = $argument->add($itemData);
            }

            return new FilteredCollection($elements);
        }

        $item              = new Item($itemData, $this->config, true);
        $this->arguments[] = $item;

        return $item;
    }

    public function __unset($key): void
    {
        foreach ($this->arguments as $argument) {
            unset($argument->{$key});
        }
    }
}
