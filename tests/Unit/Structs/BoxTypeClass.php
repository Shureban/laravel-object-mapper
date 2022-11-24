<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;

class BoxTypeClass
{
    public DateTime   $dateTime;
    public Carbon     $carbon;
    public Collection $collection;
}
