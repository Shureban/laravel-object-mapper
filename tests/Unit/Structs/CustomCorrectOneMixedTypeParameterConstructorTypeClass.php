<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class CustomCorrectOneMixedTypeParameterConstructorTypeClass
{
    public int $id;

    public function __construct(mixed $id)
    {
        $this->id = $id;
    }
}
