<?php

namespace Shureban\LaravelObjectMapper\Types\BoxTypes;

use Shureban\LaravelObjectMapper\Types\Type;

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
