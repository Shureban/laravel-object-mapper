<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Throwable;

class LossyConversionException extends ObjectMapperException
{
    /**
     * @param string         $expectedType
     * @param mixed          $value
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $expectedType, mixed $value, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Strict mode: value of type %s cannot be converted into %s without data loss', get_debug_type($value), $expectedType);

        parent::__construct($message, $code, $previous);
    }
}
