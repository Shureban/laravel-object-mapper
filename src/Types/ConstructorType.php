<?php

namespace Shureban\LaravelObjectMapper\Types;

class ConstructorType extends ObjectType implements CustomObjectTypeInterface
{
    /**
     * @param mixed $value
     *
     * @return object
     */
    public function convert(mixed $value): object
    {
        $className = $this->property->getType()->getName();

        return new $className($value);
    }
}
