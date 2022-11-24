<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;

class PhpDocBoxTypeClass
{
    /** @var DateTime */
    public $dateTime;
    /** @var Carbon */
    public $carbon;
    /** @var Collection */
    public $collection;
}
