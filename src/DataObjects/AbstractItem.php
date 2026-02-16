<?php

namespace Softonic\GraphQL\DataObjects;

use Iterator;
use JsonSerializable;
use Softonic\GraphQL\DataObjects\Interfaces\DataObject;
use Softonic\GraphQL\DataObjects\Mutation\FilteredCollection;
use Softonic\GraphQL\Traits\ItemIterator;

abstract class AbstractItem extends AbstractObject implements Iterator
{
    use ItemIterator;

    public function has(string $key): bool
    {
        $keyPath  = explode('.', $key);
        $firstKey = array_shift($keyPath);

        if (!array_key_exists($firstKey, $this->arguments)) {
            return false;
        }

        if ($keyPath === []) {
            return true;
        }

        if (!$this->arguments[$firstKey] instanceof DataObject) {
            return false;
        }

        $nextKey = implode('.', $keyPath);

        return $this->arguments[$firstKey]->has($nextKey);
    }

    public function __get(string $key): mixed
    {
        return $this->arguments[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->arguments[$key] = $value;
    }

    public function equals(array $data): bool
    {
        return $data === $this->arguments;
    }

    public function isEmpty(): bool
    {
        return $this->arguments === [];
    }

    public function jsonSerialize(): array
    {
        $item = [];
        foreach ($this->arguments as $key => $value) {
            if ($value instanceof FilteredCollection && !$value->hasChildren()) {
                continue;
            }

            if ($value instanceof JsonSerializable) {
                if (!empty($valueSerialized = $value->jsonSerialize())) {
                    $item[$key] = $valueSerialized;
                }
            } else {
                $item[$key] = $value;
            }
        }

        return $item;
    }
}
