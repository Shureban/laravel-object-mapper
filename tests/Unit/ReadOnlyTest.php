<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ReadOnlyClass;
use Tests\TestCase;

class ReadOnlyTest extends TestCase
{
    public function test_withoutType()
    {
        $this->assertEquals(0, (new ObjectMapper(new ReadOnlyClass()))->mapFromJson('{"int": 10}')->int);
    }
}
