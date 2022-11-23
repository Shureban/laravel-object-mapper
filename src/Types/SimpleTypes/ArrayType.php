<?php

namespace Shureban\LaravelObjectMapper\Types\SimpleTypes;

use Shureban\LaravelObjectMapper\Types\Type;

class ArrayType extends Type
{
    /**
     * @param mixed $value
     *
     * @return array
     */
    public function convert(mixed $value): array
    {
        return (array)$value;
    }

    /**
     * @return array
     */
    public function getDefaultValue(): array
    {
        return [];
    }
}
