<?php

namespace Shureban\LaravelObjectMapper\Types;

use DateTime;
use DateTimeZone;
use Exception;

class DateTimeType extends ObjectType
{
    /**
     * @param mixed $value
     *
     * @return DateTime
     * @throws Exception
     */
    public function convert(mixed $value): DateTime
    {
        return !empty($value)
            ? new DateTime($value, new DateTimeZone(config('app.timezone')))
            : $this->getDefaultValue();
    }
}
