<?php

namespace Softonic\GraphQL\Mutation;

use Softonic\GraphQL\Mutation\Traits\MutationObjectHandler;

class Item implements MutationObject, \JsonSerializable
{
    use MutationObjectHandler;

    private $arguments;

    private $config;

    private $hasChanged = false;

    public function __construct(array $arguments = [], array $config = [], bool $hasChanged = false)
    {
        $this->arguments  = $arguments;
        $this->config     = $config;
        $this->hasChanged = $hasChanged;
    }

    public function __get(string $key)
    {
        if ((!array_key_exists($key, $this->arguments) || ($this->arguments[$key] === null))
            && array_key_exists($key, $this->config)) {
            $mutationTypeClass = $this->config[$key]->type;

            $this->arguments[$key] = new $mutationTypeClass([], $this->config[$key]->children);
        }

        return array_key_exists($key, $this->arguments) ? $this->arguments[$key] : null;
    }

    public function __set(string $key, $value): void
    {
        if (array_key_exists($key, $this->arguments)) {
            if ($this->arguments[$key] !== $value) {
                $this->setValue($key, $value);
            }
        } else {
            $this->setValue($key, $value);
        }
    }

    private function setValue(string $key, $value): void
    {
        $this->arguments[$key] = $value;

        $this->hasChanged = true;
    }

    public function set(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function jsonSerialize(): array
    {
        if (!$this->hasChanged()) {
            return [];
        }

        $item = [];
        foreach ($this->arguments as $key => $value) {
            $item[$key] = $value instanceof \JsonSerializable ? $value->jsonSerialize() : $value;
        }

        return $item;
    }
}
