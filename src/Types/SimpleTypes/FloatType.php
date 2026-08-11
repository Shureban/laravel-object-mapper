<?php

namespace Shureban\LaravelObjectMapper\Types\SimpleTypes;

use Shureban\LaravelObjectMapper\Exceptions\InvalidValueTypeException;
use Shureban\LaravelObjectMapper\Types\Type;

class FloatType extends Type
{
    /**
     * @param mixed $value
     *
     * @return float
     * @throws InvalidValueTypeException
     */
    public function convert(mixed $value): float
    {
        if (is_array($value) || is_object($value)) {
            throw new InvalidValueTypeException('float', $value);
        }

        return (float)$value;
    }

    /**
     * @return float
     */
    public function getDefaultValue(): float
    {
        return 0.0;
    }
}
