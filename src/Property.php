<?php

namespace Shureban\LaravelObjectMapper;

use ReflectionProperty;
use Shureban\LaravelObjectMapper\Types\Factory;
use Shureban\LaravelObjectMapper\Types\Type;

class Property
{
    private const DocVarRegex = '/var\s(?<type>\w+)?\s?\$(?<name>\w+)/';

    private Type               $type;
    private ReflectionProperty $property;

    /**
     * @param ReflectionProperty $property
     */
    public function __construct(ReflectionProperty $property)
    {
        $this->property = $property;
        $this->type     = Factory::make($property);
    }

    /**
     * @return string
     */
    public function getObjectPropertyName(): string
    {
        return $this->property->getName();
    }

    /**
     * @return string
     */
    public function getDataPropertyName(): string
    {
        $regexResult = [];

        if (preg_match(self::DocVarRegex, $this->property->getDocComment(), $regexResult)) {
            return $regexResult['name'];
        }

        return $this->property->getName();
    }

    /**
     * @return mixed
     */
    public function getDefaultValue(): mixed
    {
        return $this->property->getDefaultValue() ?: $this->type->getDefaultValue();
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function convert(mixed $value): mixed
    {
        return $this->type->convert($value);
    }
}
