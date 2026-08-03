<?php

namespace Shureban\LaravelObjectMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class DateFormat
{
    /**
     * @param string $format Format accepted by DateTime::createFromFormat (e.g. "d.m.Y").
     */
    public function __construct(public readonly string $format)
    {
    }
}
