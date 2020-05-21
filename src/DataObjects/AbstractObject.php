<?php

namespace Softonic\GraphQL\DataObjects;

use JsonSerializable;
use Softonic\GraphQL\DataObjects\Interfaces\DataObject;

abstract class AbstractObject implements JsonSerializable, DataObject
{
    /**
     * @var array
     */
    protected $arguments;

    public function __construct(array $arguments = [])
    {
        $this->arguments = $arguments;
    }

    public function toArray(): array
    {
        $item = [];
        foreach ($this->arguments as $key => $value) {
            $item[$key] = $value instanceof JsonSerializable ? $value->toArray() : $value;
        }

        return $item;
    }

    abstract public function has(string $key): bool;
}
