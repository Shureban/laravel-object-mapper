<?php

namespace Shureban\LaravelObjectMapper\Types\SimpleTypes;

use Shureban\LaravelObjectMapper\Types\Type;

class BoolType extends Type
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function convert(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        }

        if (is_int($value) || is_float($value)) {
            return $value == 1;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getDefaultValue(): bool
    {
        return false;
    }
}
