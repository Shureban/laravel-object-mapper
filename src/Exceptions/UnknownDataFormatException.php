<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Exception;
use Throwable;

class UnknownDataFormatException extends Exception
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        $message = 'Unknown data type';

        parent::__construct($message, $code, $previous);
    }
}
