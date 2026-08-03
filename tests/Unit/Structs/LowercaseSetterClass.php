<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class LowercaseSetterClass
{
    public array $tags = [];

    /**
     * PHP method names are case-insensitive: this must be found as setTags.
     *
     * @param mixed $value
     * @param mixed $rawData
     *
     * @return void
     */
    public function settags(mixed $value, mixed $rawData): void
    {
        $this->tags = explode(',', $value);
    }
}
