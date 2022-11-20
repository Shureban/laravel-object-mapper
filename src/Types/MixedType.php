<?php

namespace Shureban\LaravelObjectMapper\Types;

class MixedType extends Type
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function convert(mixed $value): mixed
    {
        return $value;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue(): mixed
    {
        return null;
    }
}
