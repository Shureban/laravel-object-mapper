<?php

namespace Shureban\LaravelObjectMapper\Types;

class SimpleTypeFactory
{
    /**
     * @param string $typeName
     *
     * @return Type|null
     */
    public static function make(string $typeName): ?Type
    {
        $typeNamespace = config(sprintf('object_mapper.types.simple.%s', $typeName));

        return !is_null($typeNamespace) ? new $typeNamespace() : null;
    }
}
