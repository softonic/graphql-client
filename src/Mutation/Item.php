<?php

namespace Softonic\GraphQL\Mutation;

use Softonic\GraphQL\Traits\JsonSerializer;

class Item implements MutationObject, \JsonSerializable
{
    use JsonSerializer;

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
        if (is_null($this->arguments[$key]) && array_key_exists($key, $this->config)) {
            $mutationType = $this->config[$key]->type;

            $this->arguments[$key] = new $mutationType([], $this->config[$key]->children);
        }

        return $this->arguments[$key];
    }

    public function __set(string $key, $value)
    {
        $this->arguments[$key] = $value;

        $this->hasChanged = true;
    }

    public function hasChanged(): bool
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof MutationObject && $argument->hasChanged()) {
                return true;
            }
        }

        return $this->hasChanged;
    }
}
