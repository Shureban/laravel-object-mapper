<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class EnumTypeClass
{
    public TestEnum $strictEnum;
    /** @var TestEnum */
    public $phpDocEnum;
    /** @var TestEnum */
    public TestEnum $bothEnum;
}
