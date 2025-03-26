<?php

namespace Softonic\GraphQL\Traits;

use BadMethodCallException;

trait CollectionArrayAccess
{
    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Try using add() instead');
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->arguments);
    }

    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException('Try using remove() instead');
    }

    public function offsetGet($offset): mixed
    {
        return $this->arguments[$offset] ?? null;
    }
}
