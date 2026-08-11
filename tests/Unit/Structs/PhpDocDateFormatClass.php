<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Attributes\DateFormat;

class PhpDocDateFormatClass
{
    /** @var \DateTime */
    #[DateFormat('d/m/Y')]
    public $date;
}
