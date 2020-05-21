<?php

namespace Softonic\GraphQL\DataObjects\Mutation\Traits;

use Softonic\GraphQL\DataObjects\Mutation\MutationObject;

trait MutationObjectHandler
{
    public function hasChanged(): bool
    {
        if ($this->hasChanged) {
            return true;
        }

        foreach ($this->arguments as $argument) {
            if ($argument instanceof MutationObject && $argument->hasChanged()) {
                $this->hasChanged = true;

                return true;
            }
        }

        return false;
    }

    public function toArray(): array
    {
        $item = [];
        foreach ($this->arguments as $key => $value) {
            $item[$key] = $value instanceof \JsonSerializable ? $value->toArray() : $value;
        }

        return $item;
    }
}
