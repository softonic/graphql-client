<?php

namespace Softonic\GraphQL\DataObjects;

abstract class AbstractCollection
{
    /**
     * @var array
     */
    protected $arguments = [];

    public function __construct(array $arguments = [])
    {
        $this->arguments = $arguments;
    }

    public function count(): int
    {
        $count = 0;
        foreach ($this->arguments as $argument) {
            if ($argument instanceof AbstractCollection) {
                $count += $argument->count();
            } elseif ($argument instanceof AbstractItem) {
                ++$count;
            }
        }

        return $count;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    abstract public function has(string $key): bool;

    abstract public function toArray(): array;

    abstract public function jsonSerialize(): array;
}
