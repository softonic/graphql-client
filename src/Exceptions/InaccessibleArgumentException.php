<?php

namespace Softonic\GraphQL\Exceptions;

class InaccessibleArgumentException extends \RuntimeException
{
    public static function fromEmptyArguments(): InaccessibleArgumentException
    {
        return new self('You cannot access a non existing collection');
    }
}
