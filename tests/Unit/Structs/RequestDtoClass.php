<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Contracts\MapsFromRequest;

class RequestDtoClass implements MapsFromRequest
{
    public int $id;
    public string $name = '';
}
