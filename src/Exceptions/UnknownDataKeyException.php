<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Throwable;

class UnknownDataKeyException extends ObjectMapperException
{
    /**
     * @param string         $key
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $key, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Data key %s does not match any property of the target object', $key);

        parent::__construct($message, $code, $previous);
    }
}
