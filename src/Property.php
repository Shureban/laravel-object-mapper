<?php

namespace Shureban\LaravelObjectMapper;

use Illuminate\Support\Str;
use ReflectionProperty;
use Shureban\LaravelObjectMapper\Exceptions\UnknownPropertyTypeException;
use Shureban\LaravelObjectMapper\Types\Factory;
use Shureban\LaravelObjectMapper\Types\Type;

class Property
{
    private ?Type              $type = null;
    private PhpDoc             $phpDoc;
    private ReflectionProperty $property;

    /**
     * @param ReflectionProperty $property
     */
    public function __construct(ReflectionProperty $property)
    {
        $this->property = $property;
        $this->phpDoc   = new PhpDoc((string)$property->getDocComment());
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
     * @throws UnknownPropertyTypeException
     */
    public function getDefaultValue(): mixed
    {
        return $this->property->getDefaultValue() ?? $this->getType()->getDefaultValue();
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     * @throws UnknownPropertyTypeException
     */
    public function convert(mixed $value): mixed
    {
        return $this->getType()->convert($value);
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->property->isReadOnly();
    }

    /**
     * The type is resolved lazily: an unsupported property type (union, unknown class)
     * breaks the mapping only when a value for that property actually arrives.
     *
     * @return Type
     * @throws UnknownPropertyTypeException
     */
    public function getType(): Type
    {
        return $this->type ??= Factory::make($this->property);
    }
}
