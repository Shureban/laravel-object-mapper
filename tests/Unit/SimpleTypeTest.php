<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SimpleTypeClass;
use Tests\TestCase;

class SimpleTypeTest extends TestCase
{
    public function test_mixed()
    {
        $this->assertEquals(10, (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"mixed": 10}')->mixed);
        $this->assertEquals(['mixed' => 'type'], (new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['mixed' => ['mixed' => 'type']])->mixed);
    }

    public function test_int()
    {
        $this->assertEquals(10, (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"int": 10}')->int);
        $this->assertEquals(10, (new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['int' => 10])->int);
    }

    public function test_float()
    {
        $this->assertEquals(10.10, (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"float": 10.10}')->float);
        $this->assertEquals(10.10, (new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['float' => 10.10])->float);
    }

    public function test_string()
    {
        $this->assertEquals('text', (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"string": "text"}')->string);
        $this->assertEquals('text', (new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['string' => 'text'])->string);
    }

    public function test_bool()
    {
        $this->assertTrue((new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"bool": true}')->bool);
        $this->assertTrue((new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"bool": "true"}')->bool);
        $this->assertTrue((new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"bool": "1"}')->bool);
        $this->assertTrue((new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"bool": 1}')->bool);
        $this->assertFalse((new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"bool": false}')->bool);
        $this->assertFalse((new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"bool": "false"}')->bool);
        $this->assertFalse((new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"bool": "0"}')->bool);
        $this->assertFalse((new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"bool": 0}')->bool);
        $this->assertFalse((new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"bool": "any_other_value"}')->bool);

        $this->assertTrue((new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['bool' => true])->bool);
        $this->assertTrue((new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['bool' => "true"])->bool);
        $this->assertTrue((new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['bool' => '1'])->bool);
        $this->assertTrue((new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['bool' => 1])->bool);
        $this->assertFalse((new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['bool' => false])->bool);
        $this->assertFalse((new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['bool' => 'false'])->bool);
        $this->assertFalse((new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['bool' => '0'])->bool);
        $this->assertFalse((new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['bool' => 0])->bool);
        $this->assertFalse((new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['bool' => 'any_other_value'])->bool);
    }

    public function test_array()
    {
        $this->assertEquals([1, 2, 3, 4], (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"array": [1,2,3,4]}')->array);
        $this->assertEquals([1, 2, 3, 4], (new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['array' => [1, 2, 3, 4]])->array);
    }

    public function test_object()
    {
        $this->assertEquals((object)['value' => 10], (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"object": {"value": 10}}')->object);
        $this->assertEquals((object)['value' => 10], (new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['object' => ['value' => 10]])->object);
    }
}
