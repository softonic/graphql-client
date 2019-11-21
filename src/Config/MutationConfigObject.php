<?php

namespace Softonic\GraphQL\Config;

use Softonic\GraphQL\Traits\JsonPathAccessor;

class MutationConfigObject
{
    use JsonPathAccessor;

    public $type;

    public $linksTo;

    public $children = [];

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            if ($key === 'children') {
                foreach ($value as $propertyName => $property) {
                    $this->children[$propertyName] = new static($property);
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function __get($key)
    {
        if (property_exists(static::class, $key)) {
            return $this->{$key};
        }

        return $this->children[$key];
    }
}
