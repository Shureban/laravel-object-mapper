<?php

namespace Shureban\LaravelObjectMapper\Types\BoxTypes;

use Carbon\Carbon;
use DateTimeInterface;
use DateTimeZone;
use Illuminate\Support\Carbon as SupportCarbon;
use Shureban\LaravelObjectMapper\Exceptions\InvalidDateTimeValueException;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;
use Throwable;

class CarbonType extends ObjectType
{
    /**
     * Instantiates Illuminate\Support\Carbon (a subclass of Carbon\Carbon), so the result
     * is assignable to properties typed with either class.
     *
     * @param mixed $value
     *
     * @return Carbon
     * @throws InvalidDateTimeValueException
     */
    public function convert(mixed $value): Carbon
    {
        if ($value instanceof DateTimeInterface) {
            return new SupportCarbon($value);
        }

        if (is_int($value)) {
            return SupportCarbon::createFromTimestamp($value, $this->getTimezone());
        }

        if (empty($value) || !is_string($value)) {
            throw new InvalidDateTimeValueException(Carbon::class, $value);
        }

        try {
            return new SupportCarbon($value, $this->getTimezone());
        } catch (Throwable $exception) {
            throw new InvalidDateTimeValueException(Carbon::class, $value, 0, $exception);
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
