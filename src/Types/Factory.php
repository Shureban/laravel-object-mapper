<?php

namespace Shureban\LaravelObjectMapper\Types;

use ReflectionProperty;

class Factory
{
    public static function make(ReflectionProperty $property): Type
    {
        if (is_null($property->getType())) {
            return new Any($property);
        }

        return match ($property->getType()->getName()) {
            'string' => new Text($property),
            'float'  => new Double($property),
            'int'    => new Integer($property),
            'bool'   => new Boolean($property),
            'array'  => new ArrayType($property),
        };
    }
}
