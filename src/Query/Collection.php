<?php

namespace Softonic\GraphQL\Query;

use Softonic\GraphQL\Traits\GqlIterator;

class Collection implements ReadObject, \Iterator
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

    public function filter(array $filters): Collection
    {
        $filteredItems = array_filter($this->arguments, function ($item) use ($filters) {
            foreach ($filters as $filterKey => $filterValue) {
                if (!($item->{$filterKey} == $filterValue)) {
                    return false;
                }
            }

            return true;
        });

        return new Collection(array_values($filteredItems));
    }
}