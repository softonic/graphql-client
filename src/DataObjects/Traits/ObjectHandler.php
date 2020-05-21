<?php

namespace Softonic\GraphQL\DataObjects\Traits;

use JsonSerializable;

trait ObjectHandler
{
    public function toArray(): array
    {
        $item = [];
        foreach ($this->arguments as $key => $value) {
            $item[$key] = $value instanceof JsonSerializable ? $value->toArray() : $value;
        }

        return $item;
    }
}
