<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Attributes\EnumFallback;

class EnumFallbackClass
{
    #[EnumFallback(TestEnum::Hearts)]
    public TestEnum $suit;
}
