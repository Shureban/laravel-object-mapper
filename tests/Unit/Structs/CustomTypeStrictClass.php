<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;

class CustomTypeStrictClass
{
    public CustomTypeStrictSimpleClass $simpleClass;
    public CustomTypeStrictBoxClass    $boxClass;
    public CustomTypeStrictCustomClass $customClass;
}

class CustomTypeStrictSimpleClass
{
    public int    $int;
    public float  $float;
    public string $string;
    public bool   $bool;
    public array  $array;
    public object $object;
}

class CustomTypeStrictBoxClass
{
    public DateTime   $dateTime;
    public Carbon     $carbon;
    public Collection $collection;
}

class CustomTypeStrictCustomClass
{
    public string $key;
}
