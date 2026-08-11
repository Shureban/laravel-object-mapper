<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Carbon\Carbon;
use Shureban\LaravelObjectMapper\Attributes\ArrayOf;
use Shureban\LaravelObjectMapper\Attributes\CastWith;
use Shureban\LaravelObjectMapper\Attributes\DateFormat;

class AttributeTypesClass
{
    #[CastWith(UpperCaseType::class)]
    public string $shout;

    #[ArrayOf(CustomTypeForArrayOf::class)]
    public array $items = [];

    #[ArrayOf('int', depth: 2)]
    public array $matrix = [];

    #[DateFormat('d.m.Y')]
    public Carbon $birthday;
}
