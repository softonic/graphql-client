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

//    abstract protected function newCollection($key);

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
}
