<?php

namespace Softonic\GraphQL\Config;

class MutationsConfig
{
    /**
     * @var array
     */
    private $mutationsConfig = [];

    public function __construct(array $config)
    {
        foreach ($config as $mutationName => $mutationConfig) {
            $builder = new MutationConfigBuilder();

            $this->mutationsConfig[$mutationName] = $builder->build($mutationConfig);
        }
    }

    /**
     * @return array<MutationTypeConfig>
     */
    public function get(string $mutationName): array
    {
        return $this->mutationsConfig[$mutationName];
    }
}
