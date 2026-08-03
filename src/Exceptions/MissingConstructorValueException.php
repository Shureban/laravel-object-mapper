<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Throwable;

class MissingConstructorValueException extends ObjectMapperException
{
    /**
     * @param string         $class
     * @param array|string[] $parameters
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $class, array $parameters, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Missing required constructor values for %s: %s', $class, implode(', ', $parameters));

        parent::__construct($message, $code, $previous);
    }
}
