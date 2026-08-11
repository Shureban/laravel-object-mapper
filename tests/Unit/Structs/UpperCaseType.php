<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Types\Type;

class UpperCaseType extends Type
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function convert(mixed $value): string
    {
        return strtoupper((string)$value);
    }

    /**
     * @return string
     */
    public function getDefaultValue(): string
    {
        return '';
    }
}
