<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Throwable;

class InvalidValueTypeException extends ObjectMapperException
{
    /**
     * @param string         $expectedType
     * @param mixed          $value
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $expectedType, mixed $value, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Cannot convert value of type %s into %s', get_debug_type($value), $expectedType);

        parent::__construct($message, $code, $previous);
    }
}
