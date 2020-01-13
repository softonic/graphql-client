<?php

namespace Softonic\GraphQL\Mutation;

interface MutationObject
{
    public function set(array $data): void;

    public function has(string $key): bool;

    public function hasChanged(): bool;

    public function toArray(): array;

    public function jsonSerialize(): array;
}
