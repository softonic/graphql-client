<?php

namespace Softonic\GraphQL\Mutation;

use Softonic\GraphQL\Exceptions\InaccessibleArgumentException;

class Collection extends FilteredCollection
{
    public function __get(string $key): Collection
    {
        if (empty($this->arguments)) {
            throw InaccessibleArgumentException::fromEmptyArguments($key);
        }

        return parent::__get($key);
    }

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
}
