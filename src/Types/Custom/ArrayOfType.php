<?php

namespace Shureban\LaravelObjectMapper\Types\Custom;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;

class ArrayOfType extends ObjectType
{
    private object $type;

    public function __construct(object $type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $value
     *
     * @return object
     */
    public function convert(mixed $value): object
    {
        return (new ObjectMapper($this->type))->mapFromArray($value);
    }
}
