<?php

namespace Shureban\LaravelObjectMapper\Types;

use ReflectionClass;
use ReflectionException;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;

class CustomType extends ObjectType implements CustomObjectTypeInterface
{
    /**
     * @param mixed $value
     *
     * @return object
     * @throws ReflectionException
     */
    public function convert(mixed $value): object
    {
        $class = new ReflectionClass($this->property->getType()->getName());

        return $class->newInstanceWithoutConstructor();
    }
}
