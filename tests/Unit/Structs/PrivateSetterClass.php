<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class PrivateSetterClass
{
    public string $name;

    private function setName(string $name, mixed $rawData = null): void
    {
        $this->name = 'via_private_setter';
    }
}
