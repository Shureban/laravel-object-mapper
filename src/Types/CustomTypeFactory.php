<?php

namespace Shureban\LaravelObjectMapper\Types;

use Illuminate\Database\Eloquent\Model;
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

        if (!class_exists($typeName)) {
            return null;
        }

        $instance = (new ReflectionClass($typeName))->newInstanceWithoutConstructor();

        if ($instance instanceof Model) {
            $typeNamespace = config('object_mapper.types.other.model');

            return !is_null($typeNamespace) ? new $typeNamespace($instance) : null;
        }

        $typeNamespace = config('object_mapper.types.other.custom');

        return !is_null($typeNamespace) ? new $typeNamespace($instance) : null;
    }
}
