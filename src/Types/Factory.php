<?php

namespace Shureban\LaravelObjectMapper\Types;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Carbon as SupportCarbon;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use Shureban\LaravelObjectMapper\Attributes\ArrayOf;
use Shureban\LaravelObjectMapper\Attributes\CastWith;
use Shureban\LaravelObjectMapper\Attributes\DateFormat;
use Shureban\LaravelObjectMapper\ClassExtraInformation;
use Shureban\LaravelObjectMapper\Exceptions\UnknownPropertyTypeException;
use Shureban\LaravelObjectMapper\PhpDoc;
use Shureban\LaravelObjectMapper\Types\BoxTypes\DateFormatType;

class Factory
{
    /**
     * Resolution priority: CastWith > ArrayOf > DateFormat > phpDoc > native type.
     *
     * @param ReflectionProperty $property
     *
     * @return Type
     * @throws UnknownPropertyTypeException
     */
    public static function make(ReflectionProperty $property): Type
    {
        $attributeType = self::makeFromAttributes($property);

        if ($attributeType !== null) {
            return $attributeType;
        }

        $phpDoc = new PhpDoc((string)$property->getDocComment());

        if (!$property->hasType() && !$phpDoc->hasType()) {
            return SimpleTypeFactory::make('mixed');
        }

        if (!$phpDoc->hasType() && !($property->getType() instanceof ReflectionNamedType)) {
            throw new UnknownPropertyTypeException($property->getName());
        }

        $type     = $phpDoc->hasType() ? $phpDoc->getPropertyType() : $property->getType()->getName();
        $resolved = self::resolveByName($type, $property);

        if ($resolved === null) {
            throw new UnknownPropertyTypeException($property->getName());
        }

        return $phpDoc->isArrayOf() ? new ArrayOfType($resolved, $phpDoc->arrayNestedLevel()) : $resolved;
    }

    /**
     * Same resolution rules for a constructor parameter: CastWith > ArrayOf > DateFormat > native type.
     *
     * @param ReflectionParameter $parameter
     *
     * @return Type
     * @throws UnknownPropertyTypeException
     */
    public static function makeForParameter(ReflectionParameter $parameter): Type
    {
        $attributeType = self::makeFromAttributes($parameter);

        if ($attributeType !== null) {
            return $attributeType;
        }

        if (!$parameter->hasType()) {
            return SimpleTypeFactory::make('mixed');
        }

        if (!($parameter->getType() instanceof ReflectionNamedType)) {
            throw new UnknownPropertyTypeException($parameter->getName());
        }

        $resolved = self::resolveByName($parameter->getType()->getName(), $parameter);

        if ($resolved === null) {
            throw new UnknownPropertyTypeException($parameter->getName());
        }

        return $resolved;
    }

    /**
     * @param ReflectionProperty|ReflectionParameter $holder
     *
     * @return Type|null
     * @throws UnknownPropertyTypeException
     */
    private static function makeFromAttributes(ReflectionProperty|ReflectionParameter $holder): ?Type
    {
        $castWith = $holder->getAttributes(CastWith::class);

        if ($castWith !== []) {
            $typeClass = $castWith[0]->newInstance()->typeClass;

            return new $typeClass();
        }

        $arrayOf = $holder->getAttributes(ArrayOf::class);

        if ($arrayOf !== []) {
            $instance = $arrayOf[0]->newInstance();
            $itemType = self::resolveByName($instance->type, $holder);

            if ($itemType === null) {
                throw new UnknownPropertyTypeException($holder->getName());
            }

            return new ArrayOfType($itemType, $instance->depth);
        }

        $dateFormat     = $holder->getAttributes(DateFormat::class);
        $reflectionType = $holder->getType();

        if ($dateFormat !== [] && $reflectionType instanceof ReflectionNamedType) {
            $dateClasses = [DateTime::class, Carbon::class, SupportCarbon::class];

            if (in_array($reflectionType->getName(), $dateClasses, true)) {
                return new DateFormatType($reflectionType->getName(), $dateFormat[0]->newInstance()->format);
            }
        }

        return null;
    }

    /**
     * Shared resolution chain for a type name: simple -> box -> custom -> use-statement lookup.
     *
     * @param string                                  $type
     * @param ReflectionProperty|ReflectionParameter $holder
     *
     * @return Type|null
     */
    private static function resolveByName(string $type, ReflectionProperty|ReflectionParameter $holder): ?Type
    {
        $resolved = SimpleTypeFactory::make($type) ?? BoxTypeFactory::make($type) ?? CustomTypeFactory::make($type, $holder);

        if ($resolved !== null) {
            return $resolved;
        }

        $declaringClass = $holder->getDeclaringClass();

        if ($declaringClass === null) {
            return null;
        }

        $extraInformation = new ClassExtraInformation($declaringClass);
        $namespace        = $extraInformation->getFullObjectUseNamespace($type);

        return is_null($namespace) ? null : CustomTypeFactory::make($namespace, $holder);
    }
}
