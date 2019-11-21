<?php

namespace Softonic\GraphQL\Traits;

trait GqlIterator
{
    private $arguments;

    public function rewind()
    {
        reset($this->arguments);
    }

    public function current()
    {
        return current($this->arguments);
    }

    public function key()
    {
        return key($this->arguments);
    }

    public function next()
    {
        return next($this->arguments);
    }

    public function valid()
    {
        $key = key($this->arguments);

        return ($key !== null && $key !== false);
    }
}
