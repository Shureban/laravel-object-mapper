<?php

namespace Shureban\LaravelObjectMapper\Types;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Carbon as CarbonSupport;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Shureban\LaravelObjectMapper\ClassExtraInformation;
use Shureban\LaravelObjectMapper\Exceptions\UnknownPropertyTypeException;
use Shureban\LaravelObjectMapper\PhpDoc;
use Shureban\LaravelObjectMapper\Types\BoxTypes\CarbonType;
use Shureban\LaravelObjectMapper\Types\BoxTypes\CollectionType;
use Shureban\LaravelObjectMapper\Types\BoxTypes\DateTimeType;
use Shureban\LaravelObjectMapper\Types\Custom\ArrayOfType;
use Shureban\LaravelObjectMapper\Types\Custom\CustomType;
use Shureban\LaravelObjectMapper\Types\Custom\EnumType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ArrayType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\BoolType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\FloatType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\IntType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\StringType;

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
            return new MixedType();
        }

        $type       = $phpDoc->hasType() ? $phpDoc->getPropertyType() : $property->getType()->getName();
        $simpleType = match ($type) {
            'string'          => new StringType(),
            'float', 'double' => new FloatType(),
            'int', 'integer'  => new IntType(),
            'bool', 'boolean' => new BoolType(),
            'array'           => new ArrayType(),
            'object'          => new ObjectType(),
            default           => null,
        };

        if ($simpleType !== null) {
            return $phpDoc->isArrayOf() ? new ArrayOfType($simpleType, $phpDoc->arrayNestedLevel()) : $simpleType;
        }

        $boxType = match ($type) {
            DateTime::class                               => new DateTimeType(),
            CarbonSupport::class, Carbon::class, 'Carbon' => new CarbonType(),
            Collection::class, 'Collection'               => new CollectionType(),
            default                                       => null,
        };

        if ($boxType !== null) {
            return $phpDoc->isArrayOf() ? new ArrayOfType($boxType, $phpDoc->arrayNestedLevel()) : $boxType;
        }

        if (enum_exists($type)) {
            $type = new EnumType($type);

            return $phpDoc->isArrayOf() ? new ArrayOfType($type, $phpDoc->arrayNestedLevel()) : $type;
        }

        if (class_exists($type)) {
            $type = new CustomType((new ReflectionClass($type))->newInstanceWithoutConstructor());

            return $phpDoc->isArrayOf() ? new ArrayOfType($type, $phpDoc->arrayNestedLevel()) : $type;
        }

        $classUses = new ClassExtraInformation($property->getDeclaringClass());
        $namespace = $classUses->getFullObjectUseNamespace($type);

        if (enum_exists($namespace)) {
            $type = new EnumType($namespace);

            return $phpDoc->isArrayOf() ? new ArrayOfType($type, $phpDoc->arrayNestedLevel()) : $type;
        }

        if (class_exists($namespace)) {
            $type = new CustomType((new ReflectionClass($namespace))->newInstanceWithoutConstructor());

            return $phpDoc->isArrayOf() ? new ArrayOfType($type, $phpDoc->arrayNestedLevel()) : $type;
        }

        throw new UnknownPropertyTypeException($property->getName());
    }
}
