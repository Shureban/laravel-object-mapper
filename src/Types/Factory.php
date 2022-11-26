<?php

namespace Shureban\LaravelObjectMapper\Types;

use ReflectionException;
use ReflectionProperty;
use Shureban\LaravelObjectMapper\ClassExtraInformation;
use Shureban\LaravelObjectMapper\Exceptions\UnknownPropertyTypeException;
use Shureban\LaravelObjectMapper\PhpDoc;
use Shureban\LaravelObjectMapper\Types\Custom\ArrayOfType;

class Factory
{
    /**
     * @param ReflectionProperty $property
     *
     * @return Type
     * @throws ReflectionException|UnknownPropertyTypeException
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

        $classUses  = new ClassExtraInformation($property->getDeclaringClass());
        $namespace  = $classUses->getFullObjectUseNamespace($type);
        $customType = CustomTypeFactory::make($namespace);

        if ($customType !== null) {
            return $phpDoc->isArrayOf() ? new ArrayOfType($customType, $phpDoc->arrayNestedLevel()) : $customType;
        }

        throw new UnknownPropertyTypeException($property->getName());
    }
}
