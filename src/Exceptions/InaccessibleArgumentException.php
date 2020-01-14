<?php

namespace Softonic\GraphQL\Exceptions;

class InaccessibleArgumentException extends \RuntimeException
{
    public static function fromEmptyArguments(string $key): InaccessibleArgumentException
    {
        return new self("You cannot access a non existing collection '$key'");
    }
}
