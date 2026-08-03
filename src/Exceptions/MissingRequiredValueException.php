<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Throwable;

class MissingRequiredValueException extends ObjectMapperException
{
    /**
     * @param string         $property
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $property, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('No value provided for the required property %s', $property);

        parent::__construct($message, $code, $previous);
    }
}
