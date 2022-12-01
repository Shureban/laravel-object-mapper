<?php

namespace Shureban\LaravelObjectMapper\Types;

use ReflectionProperty;
use Shureban\LaravelObjectMapper\ClassExtraInformation;
use Shureban\LaravelObjectMapper\Exceptions\UnknownPropertyTypeException;
use Shureban\LaravelObjectMapper\PhpDoc;

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

        if (!$property->hasType() && !$phpDoc->hasType()) {
            return SimpleTypeFactory::make('mixed');
        }

        $type       = $phpDoc->hasType() ? $phpDoc->getPropertyType() : $property->getType()->getName();
        $simpleType = SimpleTypeFactory::make($type);

        if ($simpleType !== null) {
            return $phpDoc->isArrayOf() ? new ArrayOfType($simpleType, $phpDoc->arrayNestedLevel()) : $simpleType;
        }

        $boxType = BoxTypeFactory::make($type);

        if ($boxType !== null) {
            return $phpDoc->isArrayOf() ? new ArrayOfType($boxType, $phpDoc->arrayNestedLevel()) : $boxType;
        }

        $customType = CustomTypeFactory::make($type);

        if ($customType !== null) {
            return $phpDoc->isArrayOf() ? new ArrayOfType($customType, $phpDoc->arrayNestedLevel()) : $customType;
        }

        $extraInformation = new ClassExtraInformation($property->getDeclaringClass());
        $namespace        = $extraInformation->getFullObjectUseNamespace($type);
        $customType       = CustomTypeFactory::make($namespace);

        if ($customType !== null) {
            return $phpDoc->isArrayOf() ? new ArrayOfType($customType, $phpDoc->arrayNestedLevel()) : $customType;
        }

        throw new UnknownPropertyTypeException($property->getName());
    }
}
