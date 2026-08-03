<?php

namespace Shureban\LaravelObjectMapper\Support;

use Shureban\LaravelObjectMapper\Exceptions\LossyConversionException;
use Shureban\LaravelObjectMapper\Types\ArrayOfType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\BoolType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\FloatType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\IntType;
use Shureban\LaravelObjectMapper\Types\Type;

class StrictChecks
{
    private const StrictBoolValues = [true, false, 0, 1, '0', '1', 'true', 'false'];

    /**
     * Pre-conversion validation for strict mode: rejects values the loose
     * converters would silently mangle. ArrayOf types validate every item
     * against the declared item type, recursively for nested levels.
     *
     * @param Type  $type
     * @param mixed $value
     *
     * @return void
     * @throws LossyConversionException
     */
    public static function validate(Type $type, mixed $value): void
    {
        if ($type instanceof ArrayOfType && is_array($value)) {
            foreach ($value as $item) {
                $type->getNestedLevel() === 1
                    ? self::validate($type->getItemType(), $item)
                    : self::validate(new ArrayOfType($type->getItemType(), $type->getNestedLevel() - 1), $item);
            }

            return;
        }

        if ($type instanceof IntType && !is_numeric($value)) {
            throw new LossyConversionException('int', $value);
        }

        if ($type instanceof FloatType && !is_numeric($value)) {
            throw new LossyConversionException('float', $value);
        }

        if ($type instanceof BoolType && !in_array($value, self::StrictBoolValues, true)) {
            throw new LossyConversionException('bool', $value);
        }
    }
}
