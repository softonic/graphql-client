<?php

namespace Softonic\GraphQL\DataObjects\Query;

use Softonic\GraphQL\DataObjects\AbstractItem;
use Softonic\GraphQL\Traits\GqlIterator;

class Item extends AbstractItem implements QueryObject, \Iterator
{
    use GqlIterator;

    /**
     * @var array
     */
    private $arguments = [];

    public function __construct(array $arguments = [])
    {
        $this->arguments = $arguments;
    }

    public function __get($key)
    {
        return $this->arguments[$key];
    }

    public function __set($key, $value)
    {
        $this->arguments[$key] = $value;
    }
}
