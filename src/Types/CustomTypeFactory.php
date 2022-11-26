<?php

namespace Shureban\LaravelObjectMapper\Types;

use ReflectionClass;
use ReflectionException;

class CustomTypeFactory
{
    /**
     * @param string $typeName
     *
     * @return Type|null
     * @throws ReflectionException
     */
    public static function make(string $typeName): ?Type
    {
        if (enum_exists($typeName)) {
            $typeNamespace = config('object_mapper.types.other.enum');

            return !is_null($typeNamespace) ? new $typeNamespace($typeName) : null;
        }

        if (class_exists($typeName)) {
            $instance      = (new ReflectionClass($typeName))->newInstanceWithoutConstructor();
            $typeNamespace = config('object_mapper.types.other.custom');

            return !is_null($typeNamespace) ? new $typeNamespace($instance) : null;
        }

        return null;
    }
}
