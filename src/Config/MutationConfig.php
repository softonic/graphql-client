<?php

namespace Softonic\GraphQL\Config;

class MutationConfig
{
    private $configObjects;

    public function __construct(array $config)
    {
        foreach ($config as $mutationName => $mutationConfig) {
            $this->configObjects[$mutationName] = [];
            foreach ($mutationConfig as $param => $cfg) {
                $this->configObjects[$mutationName][$param] = new MutationConfigObject($cfg);
            }
        }
    }

    /**
     * @return array<MutationConfigObject>
     */
    public function get(string $mutationName): array
    {
        return $this->configObjects[$mutationName];
    }
}
