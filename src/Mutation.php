<?php

namespace Softonic\GraphQL;

use Softonic\GraphQL\Config\MutationTypeConfig;
use Softonic\GraphQL\Mutation\MutationObject;
use Softonic\GraphQL\Query\QueryObject;

class Mutation implements \JsonSerializable
{
    /**
     * @var MutationObject
     */
    private $mutation;

    /**
     * @param array<MutationTypeConfig> $config
     */
    public function __construct(array $config, QueryObject $source, bool $fromMutation = false)
    {
        $mutationBuilder = new MutationBuilder($config, $source, $fromMutation);

        $this->mutation = $mutationBuilder->build();
    }

    public function __get(string $key): MutationObject
    {
        return $this->mutation->{$key};
    }

    public function jsonSerialize(): array
    {
        return $this->mutation->jsonSerialize();
    }
}
