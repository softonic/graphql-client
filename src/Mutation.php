<?php

namespace Softonic\GraphQL;

use Softonic\GraphQL\Config\MutationConfigObject;
use Softonic\GraphQL\Mutation\Item;
use Softonic\GraphQL\Query\Item as QueryItem;

class Mutation
{
    private $mutation;

    private $config;

    /**
     * @param array<MutationConfigObject> $mutationConfig
     */
    public function __construct(array $mutationConfig, QueryItem $source)
    {
        $this->config = $mutationConfig['program'];

        $mutationBuilder = new MutationBuilder($this->config, $source);

        $this->mutation = $mutationBuilder->build();
    }

    public function __get(string $key): Item
    {
        return $this->mutation->{$key};
    }

    public function __set(string $key, $value)
    {
        $this->mutation->{$key} = $value;
    }

    public function jsonSerialize(): array
    {
        return $this->mutation->jsonSerialize();
    }
}
