<?php

namespace Softonic\GraphQL;

use Softonic\GraphQL\Config\MutationTypeConfig;
use Softonic\GraphQL\DataObjects\Mutation\Collection as MutationCollection;
use Softonic\GraphQL\DataObjects\Mutation\Item as MutationItem;
use Softonic\GraphQL\DataObjects\Mutation\MutationObject;
use Softonic\GraphQL\DataObjects\Query\Collection as QueryCollection;
use Softonic\GraphQL\DataObjects\Query\Item as QueryItem;
use Softonic\GraphQL\DataObjects\Query\QueryObject;

class Mutation
{
    const SOURCE_ROOT_PATH = '.';

    /**
     * @var array
     */
    private static $config;

    /**
     * @var bool
     */
    private static $hasChanged;

    /**
     * @var MutationTypeConfig
     */
    private static $mutationTypeConfig;

    /**
     * @param array<MutationTypeConfig> $config
     */
    public static function build(array $config, QueryObject $source, bool $fromMutation = false): MutationObject
    {
        static::$config     = $config;
        static::$hasChanged = $fromMutation;

        $mutationVariables = [];
        foreach (static::$config as $variableName => $mutationTypeConfig) {
            static::$mutationTypeConfig = $mutationTypeConfig;
            $path                       = self::SOURCE_ROOT_PATH;
            $config                     = $mutationTypeConfig->get($path);
            if ($config->type === MutationCollection::class) {
                $arguments = [];
                foreach ($source as $sourceItem) {
                    $mutationItemArguments = static::generateMutationArguments($sourceItem, $path);

                    $arguments[] = new MutationItem($mutationItemArguments, $config->children, static::$hasChanged);
                }

                $mutationVariables[$variableName] = new $config->type(
                    $arguments,
                    $config->children,
                    static::$hasChanged
                );
            } else {
                $arguments = static::generateMutationArguments($source, $path);

                $mutationVariables[$variableName] = new $config->type(
                    $arguments,
                    $config->children,
                    static::$hasChanged
                );
            }
        }

        return new MutationItem($mutationVariables, static::$config, static::$hasChanged);
    }

    private static function generateMutationArguments(QueryItem $source, string $path): array
    {
        $arguments = [];
        foreach ($source as $sourceKey => $sourceValue) {
            $childPath   = static::createPathFromParent($path, $sourceKey);
            $childConfig = static::$mutationTypeConfig->get($childPath);

            if (is_null($childConfig)) {
                break;
            }

            if ($sourceValue instanceof QueryObject) {
                if ($sourceValue instanceof QueryCollection) {
                    $arguments[$sourceKey] = static::mutateChild($childConfig, $sourceValue, $childPath);
                } else {
                    $mutationItemArguments = static::generateMutationArguments($sourceValue, $childPath);

                    $arguments[$sourceKey] = new MutationItem(
                        $mutationItemArguments,
                        $childConfig->children,
                        static::$hasChanged
                    );
                }
            } elseif ($childConfig->type === MutationTypeConfig::SCALAR_DATA_TYPE) {
                $arguments[$sourceKey] = $sourceValue;
            }
        }

        return $arguments;
    }

    private static function createPathFromParent(string $parent, string $child): string
    {
        return ('.' === $parent) ? ".{$child}" : "{$parent}.{$child}";
    }

    private static function mutateChild(
        MutationTypeConfig $config,
        QueryCollection $sourceCollection,
        string $path
    ): MutationObject {
        $arguments = [];
        if (is_null($config->linksTo)) {
            foreach ($config->children as $key => $childConfig) {
                $childPath       = static::createPathFromParent($path, $key);
                $arguments[$key] = static::mutateChild($childConfig, $sourceCollection, $childPath);
            }
        } else {
            foreach ($sourceCollection as $sourceItem) {
                $itemArguments = static::generateMutationArguments($sourceItem, $path);

                $arguments[] = new MutationItem($itemArguments, $config->children, static::$hasChanged);
            }
        }

        return new $config->type($arguments, $config->children, static::$hasChanged);
    }
}
