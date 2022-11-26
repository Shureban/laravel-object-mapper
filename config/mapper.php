<?php

use Carbon\Carbon;
use Illuminate\Support\Carbon as CarbonSupport;
use Illuminate\Support\Collection;
use Shureban\LaravelObjectMapper\Types\BoxTypes\CarbonType;
use Shureban\LaravelObjectMapper\Types\BoxTypes\CollectionType;
use Shureban\LaravelObjectMapper\Types\BoxTypes\DateTimeType;
use Shureban\LaravelObjectMapper\Types\BoxTypes\MixedType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ArrayType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\BoolType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\FloatType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\IntType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\StringType;

return [
    'types' => [
        // Simple types
        'string'             => StringType::class,
        'float'              => FloatType::class,
        'double'             => FloatType::class,
        'int'                => IntType::class,
        'integer'            => IntType::class,
        'bool'               => BoolType::class,
        'boolean'            => BoolType::class,
        'array'              => ArrayType::class,
        'object'             => ObjectType::class,
        'mixed'              => MixedType::class,

        // Box types
        DateTime::class      => DateTimeType::class,
        CarbonSupport::class => CarbonType::class,
        Carbon::class        => CarbonType::class,
        'Carbon'             => CarbonType::class,
        Collection::class    => CollectionType::class,
        'Collection'         => CollectionType::class,

        // Custom types
        'Collection'         => new CollectionType(),
    ],
];
