<?php

namespace Shureban\LaravelObjectMapper\Types\SimpleTypes;

use Shureban\LaravelObjectMapper\Exceptions\InvalidValueTypeException;
use Shureban\LaravelObjectMapper\Types\Type;
use Stringable;

class StringType extends Type
{
    /**
     * @param mixed $value
     *
     * @return string
     * @throws InvalidValueTypeException
     */
    public function convert(mixed $value): string
    {
        if (is_array($value) || (is_object($value) && !($value instanceof Stringable))) {
            throw new InvalidValueTypeException('string', $value);
        }

        return (string)$value;
    }

    /**
     * @return string
     */
    public function getDefaultValue(): string
    {
        return '';
    }
}
