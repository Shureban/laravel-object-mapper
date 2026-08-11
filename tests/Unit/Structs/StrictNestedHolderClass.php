<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Attributes\ArrayOf;

class StrictNestedHolderClass
{
    public RequiredPropsClass $inner;

    #[ArrayOf('int')]
    public array $ids = [];
}
