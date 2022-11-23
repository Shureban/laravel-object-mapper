<?php

namespace Shureban\LaravelObjectMapper\Types\SimpleTypes;

use Shureban\LaravelObjectMapper\Types\Type;

class IntType extends Type
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
