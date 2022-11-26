<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use DateTime;

class ArrayOfBoxTypeClass
{
    /** @var DateTime[] $arrayOfDate */
    public $arrayOfDate = [];
    /** @var DateTime[][] $arrayOfArrayOfDate */
    public $arrayOfArrayOfDate = [];
    /** @var DateTime[][][] $arrayOfArrayOfArrayOfDate */
    public $arrayOfArrayOfArrayOfDate = [];
}
