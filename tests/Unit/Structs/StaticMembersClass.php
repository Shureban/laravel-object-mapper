<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class StaticMembersClass
{
    public static string $staticProperty = 'initial';
    public static string $staticSetterTarget = 'initial';

    public string $name;

    public static function setStaticSetterTarget(string $value): void
    {
        self::$staticSetterTarget = 'via_static_setter';
    }
}
