<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class CustomCorrectOneEmptyTypeParameterConstructorTypeClass
{
    public int $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}
