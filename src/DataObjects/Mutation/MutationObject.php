<?php

namespace Softonic\GraphQL\DataObjects\Mutation;

interface MutationObject
{
    public function set(array $data): void;

    public function hasChanged(): bool;
}
