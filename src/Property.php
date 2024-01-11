<?php

namespace Shureban\LaravelObjectMapper;

use ReflectionProperty;
use Shureban\LaravelObjectMapper\Exceptions\UnknownPropertyTypeException;
use Shureban\LaravelObjectMapper\Types\Factory;
use Shureban\LaravelObjectMapper\Types\Type;
use Str;

class Property
{
    private Type               $type;
    private PhpDoc             $phpDoc;
    private ReflectionProperty $property;

    /**
     * @param ReflectionProperty $property
     *
     * @throws UnknownPropertyTypeException
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
    public function getOriginalName(): string
    {
        return $this->phpDoc->getPropertyName() ?: $this->getObjectPropertyName();
    }

    /**
     * @return string
     */
    public function getSnakeCaseName(): string
    {
        return Str::snake($this->getOriginalName());
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
