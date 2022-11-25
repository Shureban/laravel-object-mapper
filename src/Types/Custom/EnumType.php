<?php

namespace Shureban\LaravelObjectMapper\Types\Custom;

use BackedEnum;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;

class EnumType extends ObjectType
{
    private string $enumNamespace;

    public function __construct(string $enumNamespace)
    {
        $this->enumNamespace = $enumNamespace;
    }

    /**
     * @param mixed $value
     *
     * @return BackedEnum
     */
    public function convert(mixed $value): BackedEnum
    {
        return call_user_func([$this->enumNamespace, 'from'], $value);
    }
}
