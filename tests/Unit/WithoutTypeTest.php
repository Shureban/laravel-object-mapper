<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SimpleTypeClass;
use Tests\TestCase;

class WithoutTypeTest extends TestCase
{
    public function test_withoutType()
    {
        $this->assertEquals(10, (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"int": 10}')->int);
        $this->assertEquals(10.10, (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"float": 10.10}')->float);
        $this->assertEquals("10", (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"string": "10"}')->string);
        $this->assertEquals(true, (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"bool": true}')->bool);
        $this->assertEquals([10], (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"array": [10]}')->array);
        $this->assertEquals((object)['key' => 'value'], (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"object": {"key":"value"}}')->object);
    }
}
