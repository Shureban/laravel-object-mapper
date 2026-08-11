<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class RequiredPropsClass
{
    public int $id;
    public ?string $note;
    public string $title = 'default';
}
