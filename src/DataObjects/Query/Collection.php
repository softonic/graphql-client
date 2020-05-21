<?php

namespace Softonic\GraphQL\DataObjects\Query;

use Softonic\GraphQL\DataObjects\AbstractCollection;
use Softonic\GraphQL\Traits\GqlIterator;

class Collection extends AbstractCollection implements QueryObject, \Iterator
{
    use GqlIterator;

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
