<?php

namespace Softonic\GraphQL\DataObjects\Mutation\Traits;

use Softonic\GraphQL\DataObjects\Mutation\MutationObject;

trait MutationObjectHandler
{
    public function hasChanged(): bool
    {
        if ($this->hasChanged) {
            return true;
        }

        foreach ($this->arguments as $argument) {
            if ($argument instanceof MutationObject && $argument->hasChanged()) {
                $this->hasChanged = true;

                return true;
            }
        }

        return false;
    }
}
