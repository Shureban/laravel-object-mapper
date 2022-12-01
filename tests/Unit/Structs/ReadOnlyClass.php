<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class ReadOnlyClass
{
    public readonly int $int;

    public function __construct()
    {
        $this->int = 0;
    }
}
