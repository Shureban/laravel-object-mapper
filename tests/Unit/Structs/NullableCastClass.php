<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Attributes\CastWith;

class NullableCastClass
{
    #[CastWith(NullableCastType::class)]
    public ?string $nickname = 'preset-default';

    #[CastWith(NullableCastType::class)]
    public ?string $other;
}
