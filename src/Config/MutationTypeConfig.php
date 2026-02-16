<?php

namespace Softonic\GraphQL\Config;

use Softonic\GraphQL\Traits\JsonPathAccessor;

class MutationTypeConfig
{
    use JsonPathAccessor;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $linksTo;

    /**
     * @var array
     */
    public $children = [];

    public function __get(string $propertyName): mixed
    {
        if (property_exists(static::class, $propertyName)) {
            return $this->{$propertyName};
        }

        return $this->children[$propertyName] ?? null;
    }

    public function hasChild(string $key): bool
    {
        return array_key_exists($key, $this->children);
    }
}
