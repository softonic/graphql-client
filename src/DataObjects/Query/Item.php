<?php

namespace Softonic\GraphQL\DataObjects\Query;

use Softonic\GraphQL\DataObjects\AbstractItem;
use Softonic\GraphQL\Traits\GqlIterator;

class Item extends AbstractItem implements QueryObject, \Iterator
{
    use GqlIterator;
}
