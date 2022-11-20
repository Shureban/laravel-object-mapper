<?php

namespace Shureban\LaravelObjectMapper\Types;

use Carbon\Carbon;
use DateTime;
use ReflectionProperty;

class Factory
{
    public static function make(ReflectionProperty $property): Type
    {
        if (is_null($property->getType())) {
            return new MixedType($property);
        }

        $typeName   = $property->getType()->getName();
        $simpleType = match ($typeName) {
            'string' => new StringType($property),
            'float'  => new FloatType($property),
            'int'    => new IntType($property),
            'bool'   => new BoolType($property),
            'array'  => new ArrayType($property),
            'object' => new ObjectType($property),
            default  => null,
        };

        if ($simpleType !== null) {
            return $simpleType;
        }

        $boxObject = match ($typeName) {
            DateTime::class => new DateTimeType($property),
            Carbon::class   => new CarbonType($property),
            default         => null
        };

        if ($boxObject !== null) {
            return $boxObject;
        }

        return new WithoutConstructorType($property);
    }
}
