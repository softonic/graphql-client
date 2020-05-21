<?php

namespace Softonic\GraphQL\DataObjects;

use Softonic\GraphQL\DataObjects\Interfaces\DataObject;
use Softonic\GraphQL\DataObjects\Traits\ObjectHandler;

class AbstractItem implements DataObject
{
    use ObjectHandler;
}
