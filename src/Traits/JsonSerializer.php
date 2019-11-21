<?php

namespace Softonic\GraphQL\Traits;

trait JsonSerializer
{
    public function jsonSerialize(): array
    {
        $item = [];
        if ($this->hasChanged()) {
            foreach ($this->arguments as $key => $value) {
                if ($value instanceof \JsonSerializable) {
                    $item[$key] = $value->jsonSerialize();
                } else {
                    $item[$key] = $value;
                }
            }
        }

        return $item;
    }

    public function toArray(): array
    {
        $item = [];
        foreach ($this->arguments as $key => $value) {
            if ($value instanceof \JsonSerializable) {
                $item[$key] = $value->toArray();
            } else {
                $item[$key] = $value;
            }
        }

        return $item;
    }
}
