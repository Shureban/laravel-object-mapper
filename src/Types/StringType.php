<?php

namespace Shureban\LaravelObjectMapper\Types;

class StringType extends Type
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function convert(mixed $value): string
    {
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
