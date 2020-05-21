<?php

namespace Softonic\GraphQL\DataObjects;

use JsonSerializable;
use Softonic\GraphQL\DataObjects\Mutation\FilteredCollection;

abstract class AbstractItem extends AbstractObject
{

    public function has(string $key): bool
    {
        $keyPath  = explode('.', $key);
        $firstKey = array_shift($keyPath);

        if (!array_key_exists($firstKey, $this->arguments)) {
            return false;
        }

        if (empty($keyPath)) {
            return true;
        }

        $nextKey = implode('.', $keyPath);

        return $this->arguments[$firstKey]->has($nextKey);
    }

    public function __get(string $key)
    {
        return array_key_exists($key, $this->arguments) ? $this->arguments[$key] : null;
    }

    public function __set(string $key, $value): void
    {
        $this->arguments[$key] = $value;
    }

    public function exists(array $data): bool
    {
        return $data === $this->arguments;
    }

    public function jsonSerialize()
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
