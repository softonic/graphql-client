<?php

namespace Softonic\GraphQL;

use Softonic\GraphQL\Config\MutationTypeConfig;
use Softonic\GraphQL\Mutation\Collection as MutationCollection;
use Softonic\GraphQL\Mutation\Item as MutationItem;
use Softonic\GraphQL\Mutation\MutationObject;
use Softonic\GraphQL\Query\Collection as QueryCollection;
use Softonic\GraphQL\Query\Item as QueryItem;
use Softonic\GraphQL\Query\QueryObject;

class MutationBuilder
{
    const SOURCE_ROOT_PATH = '.';

    /**
     * @var array
     */
    private $config;

    /**
     * @var QueryObject
     */
    private $source;

    /**
     * @var MutationTypeConfig
     */
    private $mutationTypeConfig;

    /**
     * @param array<MutationTypeConfig> $config
     */
    public function __construct(array $config, QueryObject $source)
    {
        $this->config = $config;
        $this->source = $source;
    }

    public function build(): MutationObject
    {
        $mutationVariables = [];
        foreach ($this->config as $variableName => $mutationTypeConfig) {
            $this->mutationTypeConfig = $mutationTypeConfig;
            $path                     = self::SOURCE_ROOT_PATH;
            $config                   = $this->mutationTypeConfig->get($path);
            if ($config->type === MutationCollection::class) {
                $arguments = [];
                foreach ($this->source as $sourceItem) {
                    $mutationItemArguments = $this->generateMutationArguments($sourceItem, $path);

                    $arguments[] = new MutationItem($mutationItemArguments, $config->children);
                }

                $mutationVariables[$variableName] = new $config->type($arguments, $config->children);
            } else {
                $arguments = $this->generateMutationArguments($this->source, $path);

                $mutationVariables[$variableName] = new $config->type($arguments, $config->children);
            }
        }

        return new MutationItem($mutationVariables, $this->config);
    }

    private function generateMutationArguments(QueryItem $source, string $path): array
    {
        $arguments = [];
        foreach ($source as $sourceKey => $sourceValue) {
            // Having a property that is an item is not handled.
            if ($sourceValue instanceof QueryCollection) {
                $childPath   = $this->createPathFromParent($path, $sourceKey);
                $childConfig = $this->mutationTypeConfig->get($childPath);

                $arguments[$sourceKey] = $this->mutateChild($childConfig, $sourceValue, $childPath);
            } else {
                $arguments[$sourceKey] = $sourceValue;
            }
        }

        return $arguments;
    }

    private function createPathFromParent(string $parent, string $child): string
    {
        return ('.' === $parent) ? ".{$child}" : "{$parent}.{$child}";
    }

    private function mutateChild(
        MutationTypeConfig $config,
        QueryCollection $sourceCollection,
        string $path
    ): MutationObject {
        $arguments = [];
        if (is_null($config->linksTo)) {
            foreach ($config->children as $key => $childConfig) {
                $childPath       = $this->createPathFromParent($path, $key);
                $arguments[$key] = $this->mutateChild($childConfig, $sourceCollection, $childPath);
            }
        } else {
            foreach ($sourceCollection as $sourceItem) {
                $itemArguments = $this->generateMutationArguments($sourceItem, $path);

                $arguments[] = new MutationItem($itemArguments, $config->children);
            }
        }

        return new $config->type($arguments, $config->children);
    }
}
