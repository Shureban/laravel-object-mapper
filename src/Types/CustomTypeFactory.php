<?php

namespace Shureban\LaravelObjectMapper\Types;

use Illuminate\Database\Eloquent\Model;

class CustomTypeFactory
{
    /**
     * @param string $typeName
     *
     * @return Type|null
     */
    public static function make(string $typeName): ?Type
    {
        if (enum_exists($typeName)) {
            $enumType = config('object_mapper.types.other.enum');

            return !is_null($enumType) ? new $enumType($typeName) : null;
        }

        if (!class_exists($typeName)) {
            return null;
        }

        if (is_subclass_of($typeName, Model::class)) {
            $modelType = config('object_mapper.types.other.model');

            return !is_null($modelType) ? new $modelType($typeName) : null;
        }

        $typeNamespace = config('object_mapper.types.other.custom');

        return !is_null($typeNamespace) ? new $typeNamespace($typeName) : null;
    }
}
