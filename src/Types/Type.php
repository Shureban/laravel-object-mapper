<?php

namespace Shureban\LaravelObjectMapper\Types;

abstract class Type
{
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
