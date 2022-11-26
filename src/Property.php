<?php

namespace Shureban\LaravelObjectMapper;

use ReflectionException;
use ReflectionProperty;
use Shureban\LaravelObjectMapper\Types\Factory;
use Shureban\LaravelObjectMapper\Types\Type;

class Property
{
    private Type               $type;
    private PhpDoc             $phpDoc;
    private ReflectionProperty $property;

    /**
     * @param ReflectionProperty $property
     *
     * @throws ReflectionException|Exceptions\UnknownPropertyTypeException
     */
    public function __construct(ReflectionProperty $property)
    {
        $this->property = $property;
        $this->phpDoc   = new PhpDoc($property->getDocComment());
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
        return $this->phpDoc->getPropertyName() ?: $this->property->getName();
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

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->property->isReadOnly();
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }
}
