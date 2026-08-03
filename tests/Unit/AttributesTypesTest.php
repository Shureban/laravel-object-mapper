<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Carbon\Carbon;
use Shureban\LaravelObjectMapper\Exceptions\InvalidDateTimeValueException;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\AttributeTypesClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\CustomTypeForArrayOf;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\PhpDocDateFormatClass;

class AttributesTypesTest extends TestCase
{
    public function test_castWithUsesCustomType()
    {
        $result = (new ObjectMapper(new AttributeTypesClass()))->mapFromArray(['shout' => 'hello']);

        $this->assertSame('HELLO', $result->shout);
    }

    public function test_arrayOfMapsCustomClassItems()
    {
        $result = (new ObjectMapper(new AttributeTypesClass()))->mapFromArray([
            'items' => [['key' => 'a'], ['key' => 'b']],
        ]);

        $this->assertCount(2, $result->items);
        $this->assertContainsOnlyInstancesOf(CustomTypeForArrayOf::class, $result->items);
        $this->assertSame('a', $result->items[0]->key);
        $this->assertSame('b', $result->items[1]->key);
    }

    public function test_arrayOfWithDepthMapsNestedScalars()
    {
        $result = (new ObjectMapper(new AttributeTypesClass()))->mapFromArray([
            'matrix' => [['1', '2'], ['3']],
        ]);

        $this->assertSame([[1, 2], [3]], $result->matrix);
    }

    public function test_dateFormatParsesMatchingString()
    {
        $result = (new ObjectMapper(new AttributeTypesClass()))->mapFromArray(['birthday' => '31.12.1991']);

        $this->assertInstanceOf(Carbon::class, $result->birthday);
        $this->assertSame('1991-12-31', $result->birthday->format('Y-m-d'));
    }

    public function test_dateFormatRejectsWrongFormat()
    {
        $this->assertThrows(
            fn() => (new ObjectMapper(new AttributeTypesClass()))->mapFromArray(['birthday' => '1991-12-31']),
            InvalidDateTimeValueException::class
        );
        $this->assertThrows(
            fn() => (new ObjectMapper(new AttributeTypesClass()))->mapFromArray(['birthday' => 123]),
            InvalidDateTimeValueException::class
        );
    }

    public function test_dateOnlyFormatDoesNotLeakCurrentTime()
    {
        $result = (new ObjectMapper(new AttributeTypesClass()))->mapFromArray(['birthday' => '31.12.1991']);

        $this->assertSame('00:00:00', $result->birthday->format('H:i:s'));
    }

    public function test_dateFormatWorksWithPhpDocOnlyType()
    {
        $result = (new ObjectMapper(new PhpDocDateFormatClass()))->mapFromArray(['date' => '25/12/2024']);

        $this->assertInstanceOf(\DateTime::class, $result->date);
        $this->assertSame('2024-12-25', $result->date->format('Y-m-d'));
    }
}
