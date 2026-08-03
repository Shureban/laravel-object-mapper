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

        try {
            $result = $this->dateClass === DateTime::class
                ? DateTime::createFromFormat($this->format, $value, $this->getTimezone())
                : SupportCarbon::createFromFormat($this->format, $value, $this->getTimezone());
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
