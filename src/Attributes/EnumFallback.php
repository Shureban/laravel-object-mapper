<?php

namespace Shureban\LaravelObjectMapper\Attributes;

use Attribute;
use UnitEnum;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class EnumFallback
{
    /**
     * @param UnitEnum $case Enum case used when the incoming value cannot be converted.
     */
    public function __construct(public readonly UnitEnum $case)
    {
    }
}
