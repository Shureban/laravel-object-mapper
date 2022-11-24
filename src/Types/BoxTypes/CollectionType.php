<?php

namespace Shureban\LaravelObjectMapper\Types\BoxTypes;

use Illuminate\Support\Collection;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;

class CollectionType extends ObjectType
{
    /**
     * @param mixed $value
     *
     * @return Collection
     */
    public function convert(mixed $value): Collection
    {
        return collect($value);
    }
}
