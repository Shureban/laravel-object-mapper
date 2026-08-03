<?php

namespace Shureban\LaravelObjectMapper\Types\Custom;

use BackedEnum;
use Shureban\LaravelObjectMapper\Exceptions\InvalidEnumValueException;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;
use Throwable;
use UnitEnum;

class EnumType extends ObjectType
{
    private string $enumNamespace;

    public function __construct(string $enumNamespace)
    {
        $this->enumNamespace = $enumNamespace;
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
            throw new InvalidEnumValueException($this->enumNamespace, $value);
        }

        try {
            return call_user_func([$this->enumNamespace, 'from'], $value);
        } catch (Throwable $exception) {
            throw new InvalidEnumValueException($this->enumNamespace, $value, 0, $exception);
        }
    }
}
