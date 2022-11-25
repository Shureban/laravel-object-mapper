<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

enum TestEnum: string
{
    case Hearts = 'Hearts';
    case Diamonds = 'Diamonds';
    case Clubs = 'Clubs';
    case Spades = 'Spades';
}

class EnumTypeClass
{
    public TestEnum $strictEnum;
    /** @var TestEnum */
    public $phpDocEnum;
    /** @var TestEnum */
    public TestEnum $bothEnum;
}
