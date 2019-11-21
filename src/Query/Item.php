<?php

namespace Softonic\GraphQL\Query;

use Softonic\GraphQL\Traits\GqlIterator;
use Softonic\GraphQL\Traits\JsonPathAccessor;
use Softonic\GraphQL\Traits\JsonSerializer;

class Item implements ReadObject, \JsonSerializable, \Iterator
{
    use GqlIterator, JsonPathAccessor, JsonSerializer;

    private $arguments;

    public function __construct(array $arguments = [])
    {
        $this->arguments = $arguments;
    }

    public function __get($key)
    {
        return $this->arguments[$key];
    }
}
