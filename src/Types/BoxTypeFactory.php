<?php

namespace Shureban\LaravelObjectMapper\Types;

class BoxTypeFactory
{
    /**
     * @param string $typeName
     *
     * @return Type|null
     */
    public static function make(string $typeName): ?Type
    {
        $typeNamespace = config(sprintf('object_mapper.types.box.%s', $typeName));

        return !is_null($typeNamespace) ? new $typeNamespace() : null;
    }
}
