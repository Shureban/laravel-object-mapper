<?php

namespace Shureban\LaravelObjectMapper\Types;

class ArrayOfType extends Type
{
    private Type $type;
    private int  $nestedLevel;

    /**
     * @param Type $type
     * @param int  $nestedLevel
     */
    public function __construct(Type $type, int $nestedLevel = 1)
    {
        $this->type        = $type;
        $this->nestedLevel = $nestedLevel;
    }

    /**
     * @return array
     */
    public function getDefaultValue(): array
    {
        return [];
    }

    /**
     * @param mixed $value
     *
     * @return array
     */
    public function convert(mixed $value): array
    {
        return array_map(fn(mixed $nestedValue) => $this->nestedLevel === 1
            ? $this->type->convert($nestedValue)
            : (new static($this->type, $this->nestedLevel - 1))->convert($nestedValue), $value);
    }
}
