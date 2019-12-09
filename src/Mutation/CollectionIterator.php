<?php

namespace Softonic\GraphQL\Mutation;

class CollectionIterator extends \RecursiveArrayIterator
{
    public function hasChildren(): bool
    {
        $current = $this->current();
        if ($current instanceof Item) {
            return false;
        }

        if (is_array($current)) {
            return true;
        }

        return $current->hasChildren();
    }

    public function getChildren()
    {
        return parent::current()->getIterator()->getInnerIterator();
    }
}
