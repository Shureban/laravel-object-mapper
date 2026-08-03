<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class NestedReadonlyHolderClass
{
    public ReadonlyDtoClass $inner;
    public MoneyWithFromAndConstructor $price;
}
