<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Throwable;

class InvalidJsonStructureException extends ObjectMapperException
{
    /**
     * @param string         $decodedType
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $decodedType, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('JSON must decode into an object or an array, %s given', $decodedType);

        parent::__construct($message, $code, $previous);
    }
}
