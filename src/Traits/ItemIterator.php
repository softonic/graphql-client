<?php

namespace Softonic\GraphQL\Traits;

trait ItemIterator
{
    public function rewind(): void
    {
        reset($this->arguments);
    }

    public function current(): mixed
    {
        return current($this->arguments);
    }

    public function key(): int|string|null
    {
        return key($this->arguments);
    }

    public function next(): void
    {
        next($this->arguments);
    }

    public function valid(): bool
    {
        $key = key($this->arguments);

        return ($key !== null && $key !== false);
    }
}
