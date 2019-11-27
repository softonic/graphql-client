<?php

namespace Softonic\GraphQL;

use Softonic\GraphQL\Config\MutationTypeConfig;
use Softonic\GraphQL\Mutation\Item as MutationItem;
use Softonic\GraphQL\Query\Item as QueryItem;

class Mutation implements \JsonSerializable
{
    /**
     * @var MutationItem
     */
    private $mutation;

    /**
     * @param array<MutationTypeConfig> $config
     */
    public function __construct(array $config, QueryItem $source)
    {
        $mutationBuilder = new MutationBuilder($config, $source);

        $this->mutation = $mutationBuilder->build();
    }

    public function __get(string $key): MutationItem
    {
        return $this->mutation->{$key};
    }

    public function jsonSerialize(): array
    {
        return $this->mutation->jsonSerialize();
    }
}
