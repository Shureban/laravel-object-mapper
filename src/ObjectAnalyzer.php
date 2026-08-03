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
        $reflectProps = array_filter($reflectProps, fn(ReflectionProperty $property) => !$property->isStatic());

        return array_map(fn(ReflectionProperty $property) => new Property($property), array_values($reflectProps));
    }

    /**
     * @param string $setterName
     *
     * @return bool
     */
    public function hasSetter(string $setterName): bool
    {
        $reflection = new ReflectionClass($this->object);

        if (!$reflection->hasMethod($setterName)) {
            return false;
        }

        $method = $reflection->getMethod($setterName);

        return $method->isPublic() && !$method->isStatic();
    }
}
