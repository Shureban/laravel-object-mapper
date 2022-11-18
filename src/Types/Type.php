<?php

namespace Shureban\LaravelObjectMapper\Types;

use ReflectionProperty;

abstract class Type
{
    protected ReflectionProperty $property;

    /**
     * @param ReflectionProperty $property
     */
    public function __construct(ReflectionProperty $property)
    {
        $this->property = $property;
    }

    /**
     * @return mixed
     */
    abstract public function getDefaultValue(): mixed;

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    abstract public function convert(mixed $value): mixed;
}
