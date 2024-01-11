<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class ArrayOfCustomTypeClass
{
    /** @var CustomTypeForArrayOf[] $arrayOfCustomType */
    public $arrayOfCustomType = [];
    /** @var CustomTypeForArrayOf[][] $arrayOfArrayOfCustomType */
    public $arrayOfArrayOfCustomType = [];
    /** @var CustomTypeForArrayOf[][][] $arrayOfArrayOfArrayOfCustomType */
    public $arrayOfArrayOfArrayOfCustomType = [];
}
