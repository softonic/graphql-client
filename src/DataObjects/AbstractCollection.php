<?php

namespace Softonic\GraphQL\DataObjects;

use Softonic\GraphQL\DataObjects\Interfaces\DataObject;
use Softonic\GraphQL\DataObjects\Mutation\FilteredCollection;
use Softonic\GraphQL\DataObjects\Mutation\MutationObject;
use Softonic\GraphQL\DataObjects\Traits\ObjectHandler;

abstract class AbstractCollection implements DataObject, \IteratorAggregate, \JsonSerializable
{
    use ObjectHandler;

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

    public function getIterator(): \RecursiveIteratorIterator
    {
        return new \RecursiveIteratorIterator(new CollectionIterator($this->arguments));
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function has(string $key): bool
    {
        foreach ($this->arguments as $argument) {
            if ($argument->has($key)) {
                return true;
            }
        }

        return false;
    }

    public function hasItem(array $itemData): bool
    {
        foreach ($this->arguments as $argument) {
            $method = $argument instanceof FilteredCollection ? 'hasItem' : 'exists';

            if ($argument->$method($itemData)) {
                return true;
            }
        }

        return false;
    }

    public function hasChildren(): bool
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof MutationObject) {
                return true;
            }
        }

        return false;
    }

    abstract public function jsonSerialize(): array;
}
