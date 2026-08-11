<?php

namespace Shureban\LaravelObjectMapper\Exceptions;

use Throwable;

class ImplicitModelLookupException extends ObjectMapperException
{
    /**
     * @param string         $modelClass
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $modelClass, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf(
            'Mapping a %s property performs a database lookup. Opt in explicitly: add the #[FindModel] attribute to the property, or set object_mapper.implicit_model_lookup = true to restore the v1 behavior',
            $modelClass
        );

        parent::__construct($message, $code, $previous);
    }
}
