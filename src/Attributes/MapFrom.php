<?php

namespace Shureban\LaravelObjectMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class MapFrom
{
    /**
     * @param string $key Data key to read the value from. Supports dot notation ("data.attributes.name").
     */
    public function __construct(public readonly string $key)
    {
    }
}
