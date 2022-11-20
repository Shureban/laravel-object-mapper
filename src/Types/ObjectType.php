<?php

namespace Shureban\LaravelObjectMapper\Types;

class ObjectType extends Type
{
    /**
     * @param mixed $value
     *
     * @return object
     */
    public function convert(mixed $value): object
    {
        return (object)$value;
    }

    /**
     * @return object|null
     */
    public function getDefaultValue(): mixed
    {
        return null;
    }
}
