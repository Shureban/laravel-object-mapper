<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class CustomTwoRequiredParametersConstructorTypeClass
{
    public int $id;

    public function __construct(int $id, array $data)
    {
        // never will call
        exit();
    }
}
