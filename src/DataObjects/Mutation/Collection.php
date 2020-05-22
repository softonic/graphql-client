<?php

namespace Softonic\GraphQL\DataObjects\Mutation;

class Collection extends FilteredCollection
{
    public function add(array $itemData): void
    {
        if (!empty($this->arguments[0]) && $this->arguments[0] instanceof Collection) {
            foreach ($this->arguments as $argument) {
                $argument->add($itemData);
            }
        } else {
            $this->arguments[] = new Item($itemData, $this->config, true);
        }
    }

    public function __unset($key): void
    {
        foreach ($this->arguments as $argument) {
            unset($argument->{$key});
        }
    }
}
