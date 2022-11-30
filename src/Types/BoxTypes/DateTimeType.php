<?php

namespace Shureban\LaravelObjectMapper\Types\BoxTypes;

use DateTime;
use DateTimeZone;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;

class DateTimeType extends ObjectType
{
    /**
     * @param mixed $value
     *
     * @return DateTime
     */
    public function convert(mixed $value): DateTime
    {
        return !empty($value)
            ? new DateTime($value, new DateTimeZone(config('app.timezone')))
            : $this->getDefaultValue();
    }
}
