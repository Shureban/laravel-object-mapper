<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Attributes\MapFrom;

class DottedMapFromClass
{
    #[MapFrom('shippingAddress.city')]
    public string $city;
}
