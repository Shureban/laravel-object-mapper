<?php

namespace Shureban\LaravelObjectMapper\Types;

class Double extends Type
{
    /**
     * @param mixed $value
     *
     * @return float
     */
    public function convert(mixed $value): float
    {
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
