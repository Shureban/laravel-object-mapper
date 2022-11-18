<?php

namespace Shureban\LaravelObjectMapper\Types;

class Boolean extends Type
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function convert(mixed $value): bool
    {
        return match ($value) {
            true, 'true', '1' => true,
            default           => false
        };
    }

    /**
     * @return bool
     */
    public function getDefaultValue(): bool
    {
        return false;
    }
}
