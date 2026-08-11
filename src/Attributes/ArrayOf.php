<?php

namespace Shureban\LaravelObjectMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class ArrayOf
{
    /**
     * @param string $type  Item type: simple ("int"), box ("Carbon") or a class name.
     * @param int    $depth Nesting level: 1 for Type[], 2 for Type[][] etc.
     */
    public function __construct(public readonly string $type, public readonly int $depth = 1)
    {
    }
}
