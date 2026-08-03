<?php

namespace Shureban\LaravelObjectMapper\Types;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Carbon as SupportCarbon;
use ReflectionNamedType;
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
     * @param ReflectionProperty $property
     *
     * @return Type|null
     * @throws UnknownPropertyTypeException
     */
    private static function makeFromAttributes(ReflectionProperty $property): ?Type
    {
        $castWith = $property->getAttributes(CastWith::class);

        if ($castWith !== []) {
            $typeClass = $castWith[0]->newInstance()->typeClass;

            return new $typeClass();
        }

        $arrayOf = $property->getAttributes(ArrayOf::class);

        if ($arrayOf !== []) {
            $instance = $arrayOf[0]->newInstance();
            $itemType = self::resolveByName($instance->type, $property);

            if ($itemType === null) {
                throw new UnknownPropertyTypeException($property->getName());
            }

            return new ArrayOfType($itemType, $instance->depth);
        }

        $dateFormat     = $property->getAttributes(DateFormat::class);
        $reflectionType = $property->getType();

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
     * @param string             $type
     * @param ReflectionProperty $property
     *
     * @return Type|null
     */
    private static function resolveByName(string $type, ReflectionProperty $property): ?Type
    {
        $resolved = SimpleTypeFactory::make($type) ?? BoxTypeFactory::make($type) ?? CustomTypeFactory::make($type, $property);

        if ($resolved !== null) {
            return $resolved;
        }

        $extraInformation = new ClassExtraInformation($property->getDeclaringClass());
        $namespace        = $extraInformation->getFullObjectUseNamespace($type);

        return is_null($namespace) ? null : CustomTypeFactory::make($namespace, $property);
    }
}
