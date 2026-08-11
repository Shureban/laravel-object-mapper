<?php

namespace Shureban\LaravelObjectMapper\Types\BoxTypes;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Shureban\LaravelObjectMapper\Exceptions\InvalidDateTimeValueException;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;
use Throwable;

class DateTimeType extends ObjectType
{
    /**
     * @param mixed $value
     *
     * @return DateTime
     * @throws InvalidDateTimeValueException
     */
    public function convert(mixed $value): DateTime
    {
        if ($value instanceof DateTime) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return DateTime::createFromInterface($value);
        }

        if (is_int($value)) {
            $dateTime = new DateTime('@' . $value);
            $timezone = $this->getTimezone();

            return is_null($timezone) ? $dateTime : $dateTime->setTimezone($timezone);
        }

        if (empty($value) || !is_string($value)) {
            throw new InvalidDateTimeValueException(DateTime::class, $value);
        }

        try {
            return new DateTime($value, $this->getTimezone());
        } catch (Throwable $exception) {
            throw new InvalidDateTimeValueException(DateTime::class, $value, 0, $exception);
        }
    }

    /**
     * @return DateTimeZone|null
     */
    private function getTimezone(): ?DateTimeZone
    {
        $timezone = config('app.timezone');

        return is_string($timezone) ? new DateTimeZone($timezone) : null;
    }
}
