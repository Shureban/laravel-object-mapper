<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Exception;
use Throwable;

class UnknownPropertyTypeException extends Exception
{
    /**
     * @param string         $property
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $property, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Unknown property %s type', $property);

        parent::__construct($message, $code, $previous);
    }
}
