<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Throwable;

class InvalidEnumValueException extends ObjectMapperException
{
    /**
     * @param string         $enumClass
     * @param mixed          $value
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $enumClass, mixed $value, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Cannot convert value of type %s into enum %s', get_debug_type($value), $enumClass);

        parent::__construct($message, $code, $previous);
    }
}
