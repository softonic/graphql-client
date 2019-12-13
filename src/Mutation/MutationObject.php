<?php

namespace Softonic\GraphQL\Mutation;

interface MutationObject
{
    public function has(array $data): bool;

    public function set(array $data): void;

    public function hasChanged(): bool;

    public function toArray(): array;

    public function jsonSerialize(): array;
}
