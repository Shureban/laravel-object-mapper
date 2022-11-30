<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Exception;
use Throwable;

class ParseJsonException extends Exception
{
    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
