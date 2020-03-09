<?php

namespace Softonic\GraphQL\Config;

use Softonic\GraphQL\Traits\JsonPathAccessor;

class MutationTypeConfig
{
    use JsonPathAccessor;

    const SCALAR_DATA_TYPE = 'scalar';

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

    public function __get(string $propertyName)
    {
        if (property_exists(static::class, $propertyName)) {
            return $this->{$propertyName};
        }

        return $this->children[$propertyName] ?? null;
    }
}
