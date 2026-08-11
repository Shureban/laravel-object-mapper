<?php

namespace Shureban\LaravelObjectMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class CastWith
{
    /**
     * @param class-string<\Shureban\LaravelObjectMapper\Types\Type> $typeClass
     */
    public function __construct(public readonly string $typeClass)
    {
    }
}
