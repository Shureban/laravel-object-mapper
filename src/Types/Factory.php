<?php

namespace Shureban\LaravelObjectMapper\Types;

use ReflectionNamedType;
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
     * @throws UnknownPropertyTypeException
     */
    public static function make(ReflectionProperty $property): Type
    {
        $phpDoc = new PhpDoc($property->getDocComment());

        if (!$property->hasType() && !$phpDoc->hasType()) {
            return SimpleTypeFactory::make('mixed');
        }

        if (!$phpDoc->hasType() && !($property->getType() instanceof ReflectionNamedType)) {
            throw new UnknownPropertyTypeException($property->getName());
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
        $customType       = is_null($namespace) ? null : CustomTypeFactory::make($namespace);

        if ($customType !== null) {
            return $phpDoc->isArrayOf() ? new ArrayOfType($customType, $phpDoc->arrayNestedLevel()) : $customType;
        }

        throw new UnknownPropertyTypeException($property->getName());
    }
}
