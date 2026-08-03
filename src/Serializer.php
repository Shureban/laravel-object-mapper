<?php

namespace Shureban\LaravelObjectMapper;

use BackedEnum;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Shureban\LaravelObjectMapper\Attributes\DateFormat;
use Shureban\LaravelObjectMapper\Support\ClassMetadata;
use UnitEnum;

class Serializer
{
    /**
     * Reverse of the mapping: converts an object into a plain array using the same
     * naming rules (MapFrom key, phpDoc rename, property name). Uninitialized and
     * ignored properties are skipped.
     *
     * @param object $object
     *
     * @return array
     */
    public function toArray(object $object): array
    {
        $result = [];

        foreach (ClassMetadata::for($object)->getProperties() as $property) {
            if ($property->isIgnored() || !$property->isInitialized($object)) {
                continue;
            }

            $key        = $this->resolveKey($property);
            $dateFormat = $property->getAttribute(DateFormat::class);
            $value      = $this->serializeValue($object->{$property->getObjectPropertyName()}, $dateFormat);

            if (str_contains($key, '.')) {
                Arr::set($result, $key, $value);
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @param Property $property
     *
     * @return string
     */
    private function resolveKey(Property $property): string
    {
        $originalName = $property->getOriginalName();

        if ($originalName !== $property->getObjectPropertyName()) {
            return $originalName;
        }

        return config('object_mapper.serialize_snake_case') === true ? Str::snake($originalName) : $originalName;
    }

    /**
     * @param mixed           $value
     * @param DateFormat|null $dateFormat
     *
     * @return mixed
     */
    private function serializeValue(mixed $value, ?object $dateFormat): mixed
    {
        return match (true) {
            $value instanceof BackedEnum        => $value->value,
            $value instanceof UnitEnum          => $value->name,
            $value instanceof DateTimeInterface => $value->format($dateFormat instanceof DateFormat ? $dateFormat->format : 'c'),
            $value instanceof Model             => $value->getKey(),
            $value instanceof Collection        => $value->values()->map(fn(mixed $item) => $this->serializeValue($item, null))->toArray(),
            is_array($value)                    => array_map(fn(mixed $item) => $this->serializeValue($item, null), $value),
            is_object($value)                   => $this->toArray($value),
            default                             => $value,
        };
    }
}
