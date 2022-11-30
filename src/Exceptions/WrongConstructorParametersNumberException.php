<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Exception;
use Throwable;

class WrongConstructorParametersNumberException extends Exception
{
    /**
     * @param string         $class
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $class, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Wrong constructor parameters number: %s', $class);

        parent::__construct($message, $code, $previous);
    }
}
