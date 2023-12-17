<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class CustomCorrectOneIntTypeParameterConstructorTypeClass
{
    public int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
