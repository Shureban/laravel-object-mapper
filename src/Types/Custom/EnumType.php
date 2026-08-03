<?php

namespace Shureban\LaravelObjectMapper\Types\Custom;

use BackedEnum;
use Shureban\LaravelObjectMapper\Exceptions\InvalidEnumValueException;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;
use Throwable;
use UnitEnum;

class EnumType extends ObjectType
{
    private string    $enumNamespace;
    private ?UnitEnum $fallback;

    /**
     * @param string        $enumNamespace
     * @param UnitEnum|null $fallback Case returned when the value cannot be converted.
     */
    public function __construct(string $enumNamespace, ?UnitEnum $fallback = null)
    {
        $this->enumNamespace = $enumNamespace;
        $this->fallback      = $fallback;
    }

    /**
     * @param mixed $value
     *
     * @return UnitEnum
     * @throws InvalidEnumValueException
     */
    public function convert(mixed $value): UnitEnum
    {
        if ($value instanceof $this->enumNamespace) {
            return $value;
        }

        if (!is_subclass_of($this->enumNamespace, BackedEnum::class)) {
            return $this->fallback ?? throw new InvalidEnumValueException($this->enumNamespace, $value);
        }

        try {
            return call_user_func([$this->enumNamespace, 'from'], $value);
        } catch (Throwable $exception) {
            return $this->fallback ?? throw new InvalidEnumValueException($this->enumNamespace, $value, 0, $exception);
        }
    }
}
