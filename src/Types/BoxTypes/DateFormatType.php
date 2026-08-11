<?php

namespace Shureban\LaravelObjectMapper\Types\BoxTypes;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Carbon as SupportCarbon;
use Shureban\LaravelObjectMapper\Exceptions\InvalidDateTimeValueException;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;
use Throwable;

class DateFormatType extends ObjectType
{
    private string $dateClass;
    private string $format;

    /**
     * @param string $dateClass DateTime, Carbon\Carbon or Illuminate\Support\Carbon
     * @param string $format
     */
    public function __construct(string $dateClass, string $format)
    {
        $this->dateClass = $dateClass;
        $this->format    = $format;
    }

    /**
     * @param mixed $value
     *
     * @return DateTime|Carbon
     * @throws InvalidDateTimeValueException
     */
    public function convert(mixed $value): DateTime|Carbon
    {
        if (!is_string($value)) {
            throw new InvalidDateTimeValueException($this->dateClass, $value);
        }

        // Reset unspecified fields to zero instead of the current wall-clock time,
        // so a date-only format always produces a deterministic 00:00:00.
        $format = str_contains($this->format, '!') || str_contains($this->format, '|') ? $this->format : '!' . $this->format;

        try {
            $result = $this->dateClass === DateTime::class
                ? DateTime::createFromFormat($format, $value, $this->getTimezone())
                : SupportCarbon::createFromFormat($format, $value, $this->getTimezone());
        } catch (Throwable $exception) {
            throw new InvalidDateTimeValueException($this->dateClass, $value, 0, $exception);
        }

        if ($result === false || $result === null) {
            throw new InvalidDateTimeValueException($this->dateClass, $value);
        }

        return $result;
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
