<?php

namespace Shureban\LaravelObjectMapper\Types;

use Carbon\Carbon;
use DateTime;
use ReflectionProperty;
use Shureban\LaravelObjectMapper\PhpDoc;
use Shureban\LaravelObjectMapper\Types\BoxTypes\CarbonType;
use Shureban\LaravelObjectMapper\Types\BoxTypes\DateTimeType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ArrayType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\BoolType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\FloatType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\IntType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\StringType;

class Factory
{
    /**
     * @param ReflectionProperty $property
     *
     * @return Type
     */
    public static function make(ReflectionProperty $property): Type
    {
        $phpDoc = new PhpDoc($property->getDocComment());

        if (is_null($property->getType()) && is_null($phpDoc->getPropertyType())) {
            return new MixedType();
        }

        $type = is_null($property->getType()) ? $phpDoc->getPropertyType() : $property->getType()->getName();

        return match ($type) {
            'string'          => new StringType(),
            'float', 'double' => new FloatType(),
            'int', 'integer'  => new IntType(),
            'bool', 'boolean' => new BoolType(),
            'array'           => new ArrayType(),
            'object'          => new ObjectType(),

            DateTime::class   => new DateTimeType(),
            Carbon::class     => new CarbonType(),
            
            default           => new CustomType(),
        };
    }
}
