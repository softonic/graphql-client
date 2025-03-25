<?php

namespace Softonic\GraphQL;

use Softonic\GraphQL\Config\MutationTypeConfig;
use Softonic\GraphQL\DataObjects\Mutation\Collection as MutationCollection;
use Softonic\GraphQL\DataObjects\Mutation\Item as MutationItem;
use Softonic\GraphQL\DataObjects\Mutation\MutationObject;
use Softonic\GraphQL\DataObjects\Query\Item as QueryItem;
use Softonic\GraphQL\DataObjects\Query\QueryObject;

class Mutation
{
    const SOURCE_ROOT_PATH = '.';

    private static ?array $config = null;

    private static ?bool $hasChanged = null;

    /**
     * @var MutationTypeConfig
     */
    private static $mutationTypeConfig;

    /**
     * @param array<MutationTypeConfig> $config
     */
    public static function build(array $config, QueryObject $source, bool $fromMutation = false): MutationObject
    {
        self::$config     = $config;
        self::$hasChanged = $fromMutation;

        $mutationVariables = [];
        foreach (self::$config as $variableName => $mutationTypeConfig) {
            self::$mutationTypeConfig = $mutationTypeConfig;
            $path                     = self::SOURCE_ROOT_PATH;
            $config                   = $mutationTypeConfig->get($path);
            if ($config->type === MutationCollection::class) {
                $arguments = [];
                foreach ($source as $sourceItem) {
                    $mutationItemArguments = self::generateMutationArguments($sourceItem, $path);

                    $arguments[] = new MutationItem($mutationItemArguments, $config->children, self::$hasChanged);
                }

                $mutationVariables[$variableName] = new $config->type(
                    $arguments,
                    $config->children,
                    self::$hasChanged
                );
            } else {
                $arguments = self::generateMutationArguments($source, $path);

                $mutationVariables[$variableName] = new $config->type(
                    $arguments,
                    $config->children,
                    self::$hasChanged
                );
            }
        }

        return new MutationItem($mutationVariables, self::$config, self::$hasChanged);
    }

    private static function generateMutationArguments(QueryItem $source, string $path): array
    {
        $arguments = [];
        foreach ($source as $sourceKey => $sourceValue) {
            $childPath   = self::createPathFromParent($path, $sourceKey);
            $childConfig = self::$mutationTypeConfig->get($childPath);

            if (is_null($childConfig)) {
                continue;
            }

            if (self::hasChildrenToMutate($childConfig, $sourceValue)) {
                $mutatedChild = self::mutateChild($childConfig, $sourceValue, $childPath);
                if (!is_null($mutatedChild)) {
                    $arguments[$sourceKey] = $mutatedChild;
                }
            } else {
                if ($sourceValue instanceof QueryObject) {
                    $sourceValue = $sourceValue->toArray();
                }

                $arguments[$sourceKey] = $sourceValue;
            }
        }

        return $arguments;
    }

    private static function createPathFromParent(string $parent, string $child): string
    {
        return ('.' === $parent) ? ".{$child}" : "{$parent}.{$child}";
    }

    private static function hasChildrenToMutate(MutationTypeConfig $childConfig, $sourceValue): bool
    {
        return !is_null($childConfig->type) && !is_null($sourceValue);
    }

    private static function mutateChild(
        MutationTypeConfig $config,
        QueryObject $sourceObject,
        string $path
    ): ?MutationObject {
        if (is_null($config->linksTo)) {
            $arguments = [];
            foreach ($config->children as $key => $childConfig) {
                if (!is_null($childConfig->type)) {
                    $childPath    = self::createPathFromParent($path, $key);
                    $mutatedChild = self::mutateChild($childConfig, $sourceObject, $childPath);
                    if (!is_null($mutatedChild)) {
                        $arguments[$key] = $mutatedChild;
                    }
                }
            }

            if ($arguments === []) {
                return null;
            }

            return new $config->type($arguments, $config->children, self::$hasChanged);
        }

        if ($sourceObject instanceof QueryItem) {
            return self::mutateItem($sourceObject, $path, $config);
        }

        $arguments = [];
        foreach ($sourceObject as $sourceItem) {
            $arguments[] = self::mutateItem($sourceItem, $path, $config);
        }

        return new $config->type($arguments, $config->children, self::$hasChanged);
    }

    private static function mutateItem(
        QueryItem $sourceItem,
        string $path,
        MutationTypeConfig $config
    ): MutationItem {
        $itemArguments = self::generateMutationArguments($sourceItem, $path);

        return new MutationItem($itemArguments, $config->children, self::$hasChanged);
    }
}
