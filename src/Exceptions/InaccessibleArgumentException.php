<?php

namespace Softonic\GraphQL\Exceptions;

use RuntimeException;

class InaccessibleArgumentException extends RuntimeException
{
    public static function fromEmptyArguments(string $key): InaccessibleArgumentException
    {
        return new self("You cannot access a non existing collection '{$key}'");
    }
}
