<?php

namespace Softonic\GraphQL\DataObjects;

use InvalidArgumentException;
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

        if ($isValid && $this->current()->isEmpty()) {
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

        if ($current instanceof AbstractCollection) {
            return $current->hasChildren();
        }

        throw new InvalidArgumentException("Collections only can contain Items or other Collection, instead '{$current}' value found");
    }

    public function getChildren(): ?RecursiveArrayIterator
    {
        return $this->current()
            ->getIterator()
            ->getInnerIterator();
    }
}
