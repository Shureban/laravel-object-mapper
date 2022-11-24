<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\PhpDocSimpleTypeClass;
use Tests\TestCase;

class PhpDocSimpleTypeTest extends TestCase
{
    public function test_int()
    {
        $this->assertEquals(10, (new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"int": 10}')->int);
        $this->assertEquals(10, (new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['int' => 10])->int);
    }

    public function test_float()
    {
        $this->assertEquals(10.10, (new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"float": 10.10}')->float);
        $this->assertEquals(10.10, (new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['float' => 10.10])->float);
    }

    public function test_string()
    {
        $this->assertEquals('text', (new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"string": "text"}')->string);
        $this->assertEquals('text', (new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['string' => 'text'])->string);
    }

    public function test_bool()
    {
        $this->assertTrue((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"bool": true}')->bool);
        $this->assertTrue((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"bool": "true"}')->bool);
        $this->assertTrue((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"bool": "1"}')->bool);
        $this->assertTrue((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"bool": 1}')->bool);
        $this->assertFalse((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"bool": false}')->bool);
        $this->assertFalse((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"bool": "false"}')->bool);
        $this->assertFalse((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"bool": "0"}')->bool);
        $this->assertFalse((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"bool": 0}')->bool);
        $this->assertFalse((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"bool": "any_other_value"}')->bool);

        $this->assertTrue((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['bool' => true])->bool);
        $this->assertTrue((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['bool' => "true"])->bool);
        $this->assertTrue((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['bool' => '1'])->bool);
        $this->assertTrue((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['bool' => 1])->bool);
        $this->assertFalse((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['bool' => false])->bool);
        $this->assertFalse((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['bool' => 'false'])->bool);
        $this->assertFalse((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['bool' => '0'])->bool);
        $this->assertFalse((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['bool' => 0])->bool);
        $this->assertFalse((new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['bool' => 'any_other_value'])->bool);
    }

    public function test_array()
    {
        $this->assertEquals([1, 2, 3, 4], (new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"array": [1,2,3,4]}')->array);
        $this->assertEquals([1, 2, 3, 4], (new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['array' => [1, 2, 3, 4]])->array);
    }

    public function test_object()
    {
        $this->assertEquals((object)['value' => 10], (new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromJson('{"object": {"value": 10}}')->object);
        $this->assertEquals((object)['value' => 10], (new ObjectMapper(new PhpDocSimpleTypeClass()))->mapFromArray(['object' => ['value' => 10]])->object);
    }
}
