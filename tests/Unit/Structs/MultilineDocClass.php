<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class MultilineDocClass
{
    /**
     * @var int
     */
    public $count;

    /** @var ?string */
    public $nullableName;

    /** @var int|null */
    public $unionWithNull;

    /**
     * Description mentioning $dollarWord and [] brackets.
     *
     * @var int[]
     */
    public array $numbers = [];
}
