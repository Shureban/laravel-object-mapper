<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Throwable;

class UnknownDataFormatException extends ObjectMapperException
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        $message = 'Unknown data type';

        parent::__construct($message, $code, $previous);
    }
}
