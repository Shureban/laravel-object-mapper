<?php

namespace Shureban\LaravelObjectMapper;

use ReflectionClass;
use ReflectionProperty;

class ObjectAnalyzer
{
    private object $object;

    public function __construct(object $object)
    {
        $this->object = $object;
    }

    /**
     * @return array|Property[]
     */
    public function getProperties(): array
    {
        $reflect      = new ReflectionClass($this->object);
        $reflectProps = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

        return array_map(fn(ReflectionProperty $property) => new Property($property), $reflectProps);
    }

    /**
     * @param string $setterName
     *
     * @return bool
     */
    public function hasSetter(string $setterName): bool
    {
        return (new ReflectionClass($this->object))->hasMethod($setterName);
    }
}
