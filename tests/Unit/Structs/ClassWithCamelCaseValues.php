<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class ClassWithCamelCaseValues
{
    public int     $fromRequestInt;
    public string  $fromRequestString;
    public bool    $fromRequestBool;
    public ?string $fromRequestNull = null;
}
