<?php

namespace Shureban\LaravelObjectMapper\Types\SimpleTypes;

use Shureban\LaravelObjectMapper\Exceptions\InvalidValueTypeException;
use Shureban\LaravelObjectMapper\Types\Type;

class IntType extends Type
{
    /**
     * @param mixed $value
     *
     * @return int
     * @throws InvalidValueTypeException
     */
    public function convert(mixed $value): int
    {
        if (is_array($value) || is_object($value)) {
            throw new InvalidValueTypeException('int', $value);
        }

        return (int)$value;
    }

    /**
     * @return int
     */
    public function getDefaultValue(): int
    {
        return 0;
    }
}
