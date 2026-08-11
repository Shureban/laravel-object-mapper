<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Throwable;

class InvalidDateTimeValueException extends ObjectMapperException
{
    /**
     * @param string         $dateTimeClass
     * @param mixed          $value
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $dateTimeClass, mixed $value, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Cannot convert value of type %s into %s', get_debug_type($value), $dateTimeClass);

        parent::__construct($message, $code, $previous);
    }
}
