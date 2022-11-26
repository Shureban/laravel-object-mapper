<?php

namespace Shureban\LaravelObjectMapper;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Shureban\LaravelObjectMapper\Exceptions\UnknownPropertyTypeException;

class ObjectAnalyzer
{
    private object $result;

    public function __construct(object $result)
    {
        $this->result = $result;
    }

    /**
     * @return array|Property[]
     * @throws UnknownPropertyTypeException
     * @throws ReflectionException
     */
    public function getProperties(): array
    {
        $reflect      = new ReflectionClass($this->result);
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
        return (new ReflectionClass($this->result))->hasMethod($setterName);
    }
}
