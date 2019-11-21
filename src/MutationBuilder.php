<?php

namespace Softonic\GraphQL;

use Softonic\GraphQL\Config\MutationConfigObject;
use Softonic\GraphQL\Mutation\Collection as MutationCollection;
use Softonic\GraphQL\Mutation\Item as MutationItem;
use Softonic\GraphQL\Mutation\MutationObject;
use Softonic\GraphQL\Query\Collection as QueryCollection;
use Softonic\GraphQL\Query\Item as QueryItem;
use Softonic\GraphQL\Query\ReadObject;

class MutationBuilder
{
    const MUTATION_TYPE_MAP = [
        QueryItem::class       => MutationItem::class,
        QueryCollection::class => MutationCollection::class,
    ];

    const DEFAULT_MUTATION_TYPE = MutationItem::class;

    private $config;

    private $source;

    public function __construct(MutationConfigObject $config, QueryItem $source)
    {
        $this->config = $config;
        $this->source = $source;
    }

    public function build(): MutationItem
    {
        $path   = $this->config->linksTo;
        $config = $this->config->get($path);

        $mutationType = $this->getMutationType($config, $this->source);
        $arguments    = $this->generateMutationArguments($config, $this->source, $path);

        return new $mutationType($arguments, $config->children);
    }

    private function getMutationType(MutationConfigObject $config, ReadObject $source): string
    {
        return !empty($config->linksTo) ? self::MUTATION_TYPE_MAP[get_class($source)] : self::DEFAULT_MUTATION_TYPE;
    }

    private function generateMutationArguments(MutationConfigObject $config, ReadObject $source, string $path): array
    {
        $arguments = [];
        foreach ($source as $sourceKey => $sourceValue) {
            if ($sourceValue instanceof ReadObject) {
                $childPath = $this->createPathFromParent($path, $sourceKey);

                $arguments[$sourceKey] = $this->mutateChild($config->children[$sourceKey], $sourceValue, $childPath);
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

    private function mutateChild(MutationConfigObject $config, ReadObject $source, string $path): MutationObject
    {
        if (is_null($config->linksTo)) {
            $arguments = [];
            foreach ($config->children as $key => $childConfig) {
                $childArguments = [];
                foreach ($source as $sourceKey => $sourceValue) {
                    $childChildrenType      = $this->getMutationType($childConfig, $sourceValue);
                    $childChildrenArguments = $this->generateMutationArguments($childConfig, $sourceValue, $path);

                    $childArguments[$sourceKey] = new $childChildrenType(
                        $childChildrenArguments,
                        $childConfig->children
                    );
                }

                $childType = $this->getMutationType($childConfig, $source);

                $arguments[$key] = new $childType($childArguments, $childConfig->children);
            }
        }

        $type = $this->getMutationType($config, $source);

        return new $type($arguments, $config->children);
    }
}
