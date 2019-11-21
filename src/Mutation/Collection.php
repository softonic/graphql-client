<?php

namespace Softonic\GraphQL\Mutation;

class Collection extends FilteredCollection
{
    public function __get(string $key)
    {
        if (empty($this->arguments)) {
            throw new \Exception('You cannot access a non existing collection');
        }

        return parent::__get($key);
    }

    public function add(array $itemData)
    {
        if (!empty($this->arguments[0]) && $this->arguments[0] instanceof Collection) {
            foreach ($this->arguments as $argument) {
                $argument->add($itemData);
            }
        } else {
            $this->arguments[] = new Item($itemData, $this->config, true);
        }
    }
}
