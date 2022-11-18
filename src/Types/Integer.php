<?php

namespace Shureban\LaravelObjectMapper\Types;

class Integer extends Type
{
    /**
     * @param mixed $value
     *
     * @return int
     */
    public function convert(mixed $value): int
    {
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
