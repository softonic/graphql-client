<?php

namespace Softonic\GraphQL\DataObjects;

abstract class AbstractObject implements \JsonSerializable
{
    /**
     * @var array
     */
    protected $arguments;

    public function __construct(array $arguments = [])
    {
        $this->arguments = $arguments;
    }

    abstract public function has(string $key): bool;
}
