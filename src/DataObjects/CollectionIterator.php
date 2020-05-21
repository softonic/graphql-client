<?php

namespace Softonic\GraphQL\DataObjects;

use RecursiveArrayIterator;

class CollectionIterator extends RecursiveArrayIterator
{
    public function valid(): bool
    {
        $isValid = parent::valid();
        if ($isValid && !$this->hasChildren() && $this->current() instanceof AbstractCollection) {
            $this->next();

            return $this->valid();
        }

        return $isValid;
    }

    public function hasChildren(): bool
    {
        $current = $this->current();
        if ($current instanceof AbstractItem) {
            return false;
        }

        if (is_array($current)) {
            return true;
        }

        return $current->hasChildren();
    }

    public function getChildren()
    {
        return parent::current()
            ->getIterator()
            ->getInnerIterator();
    }
}
