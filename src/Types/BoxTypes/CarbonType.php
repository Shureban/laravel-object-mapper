<?php

namespace Shureban\LaravelObjectMapper\Types\BoxTypes;

use Carbon\Carbon;
use DateTimeZone;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;

class CarbonType extends ObjectType
{
    /**
     * @param mixed $value
     *
     * @return Carbon
     */
    public function convert(mixed $value): Carbon
    {
        return !empty($value)
            ? new Carbon($value, new DateTimeZone(config('app.timezone')))
            : $this->getDefaultValue();
    }
}
