<?php

namespace Shureban\LaravelObjectMapper\Types;

use Carbon\Carbon;
use DateTimeZone;
use Exception;

class CarbonType extends ObjectType
{
    /**
     * @param mixed $value
     *
     * @return Carbon
     * @throws Exception
     */
    public function convert(mixed $value): Carbon
    {
        return !empty($value)
            ? new Carbon($value, new DateTimeZone(config('app.timezone')))
            : $this->getDefaultValue();
    }
}
