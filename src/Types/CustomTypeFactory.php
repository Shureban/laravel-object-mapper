<?php

namespace Shureban\LaravelObjectMapper\Types;

use Illuminate\Database\Eloquent\Model;
use ReflectionParameter;
use ReflectionProperty;
use Shureban\LaravelObjectMapper\Attributes\EnumFallback;
use Shureban\LaravelObjectMapper\Attributes\FindModel;
use Shureban\LaravelObjectMapper\Exceptions\ImplicitModelLookupException;

class CustomTypeFactory
{
    /**
     * @param string                                       $typeName
     * @param ReflectionProperty|ReflectionParameter|null $property Source member — used to read EnumFallback/FindModel attributes.
     *
     * @return Type|null
     * @throws ImplicitModelLookupException
     */
    public static function make(string $typeName, ReflectionProperty|ReflectionParameter|null $property = null): ?Type
    {
        if (enum_exists($typeName)) {
            $enumType = config('object_mapper.types.other.enum');
            $fallback = null;

            if ($property !== null) {
                $attributes = $property->getAttributes(EnumFallback::class);
                $fallback   = $attributes === [] ? null : $attributes[0]->newInstance()->case;
            }

            return !is_null($enumType) ? new $enumType($typeName, $fallback) : null;
        }

        if (!class_exists($typeName)) {
            return null;
        }

        if (is_subclass_of($typeName, Model::class)) {
            $optedIn = $property !== null && $property->getAttributes(FindModel::class) !== [];

            if (!$optedIn && config('object_mapper.implicit_model_lookup') !== true) {
                throw new ImplicitModelLookupException($typeName);
            }

            $modelType = config('object_mapper.types.other.eloquent');

            return !is_null($modelType) ? new $modelType($typeName) : null;
        }

        $typeNamespace = config('object_mapper.types.other.custom');

        return !is_null($typeNamespace) ? new $typeNamespace($typeName) : null;
    }
}
