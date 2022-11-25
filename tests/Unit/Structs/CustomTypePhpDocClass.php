<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;

class CustomTypePhpDocClass
{
    /** @var CustomTypePhpDocSimpleClass */
    public $simpleClass;
    /** @var CustomTypePhpDocBoxClass */
    public $boxClass;
    /** @var CustomTypePhpDocCustomClass */
    public $customClass;
}

class CustomTypePhpDocSimpleClass
{
    /** @var int */
    public $int;
    /** @var float */
    public $float;
    /** @var string */
    public $string;
    /** @var bool */
    public $bool;
    /** @var array */
    public $array;
    /** @var object */
    public $object;
}

class CustomTypePhpDocBoxClass
{
    /** @var DateTime */
    public $dateTime;
    /** @var Carbon */
    public $carbon;
    /** @var Collection */
    public $collection;
}

class CustomTypePhpDocCustomClass
{
    public string $key;
}
