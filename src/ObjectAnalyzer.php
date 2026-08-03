<?php

namespace Shureban\LaravelObjectMapper;

use Shureban\LaravelObjectMapper\Support\ClassMetadata;

class ObjectAnalyzer
{
    private object $object;

    public function __construct(object $object)
    {
        $this->object = $object;
    }

    /**
     * @return array|Property[]
     */
    public function getProperties(): array
    {
        return ClassMetadata::for($this->object)->getProperties();
    }

    /**
     * @param string $setterName
     *
     * @return bool
     */
    public function hasSetter(string $setterName): bool
    {
        return ClassMetadata::for($this->object)->hasPublicSetter($setterName);
    }
}
