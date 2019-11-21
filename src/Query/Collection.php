<?php

namespace Softonic\GraphQL\Query;

use Softonic\GraphQL\Traits\GqlIterator;
use Softonic\GraphQL\Traits\JsonSerializer;

class Collection implements ReadObject, \JsonSerializable, \Iterator
{
    use GqlIterator, JsonSerializer;

    private $arguments;

    public function __construct(array $arguments = [])
    {
        $this->arguments = $arguments;
    }
}
