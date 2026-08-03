<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Types\Type;

class NullableCastType extends Type
{
    /**
     * @param mixed $value
     *
     * @return string|null
     */
    public function convert(mixed $value): ?string
    {
        return $value === 'N/A' ? null : strtoupper($value);
    }

    /**
     * @return string|null
     */
    public function getDefaultValue(): ?string
    {
        return null;
    }
}
