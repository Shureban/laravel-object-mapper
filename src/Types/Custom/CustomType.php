<?php

namespace Shureban\LaravelObjectMapper\Types\Custom;

use ReflectionException;
use Shureban\LaravelObjectMapper\Exceptions\UnknownPropertyTypeException;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;

class CustomType extends ObjectType
{
    private object $customObject;

    public function __construct(object $customObject)
    {
        $this->customObject = $customObject;
    }

    /**
     * @param mixed $value
     *
     * @return object
     * @throws ReflectionException
     * @throws UnknownPropertyTypeException
     */
    public function convert(mixed $value): object
    {
        return (new ObjectMapper($this->customObject))->mapFromArray($value);
    }
}
