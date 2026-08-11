<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Throwable;

class InvalidModelKeyException extends ObjectMapperException
{
    /**
     * @param string         $modelClass
     * @param mixed          $value
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $modelClass, mixed $value, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Cannot use value of type %s as a primary key of model %s', get_debug_type($value), $modelClass);

        parent::__construct($message, $code, $previous);
    }
}
