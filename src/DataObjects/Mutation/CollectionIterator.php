<?php

namespace Softonic\GraphQL\DataObjects\Mutation;

class CollectionIterator extends \RecursiveArrayIterator
{
    public function valid(): bool
    {
        $isValid = parent::valid();
        if ($isValid && !$this->hasChildren() && $this->current() instanceof FilteredCollection) {
            $this->next();

            return $this->valid();
        }

        return $isValid;
    }

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
