<?php

namespace Shureban\LaravelObjectMapper\Tests\Structs;

use DateTime;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ArrayOfBoxTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ArrayOfCustomTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ArrayOfEnumTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ArrayOfSimpleTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\CustomTypeForArrayOf;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\EnumTypeForArrayOf;
use Tests\TestCase;

class ArrayOfTypeTest extends TestCase
{
    public function test_SimpleType()
    {
        $this->assertEquals([1, 2, 3, 4], (new ObjectMapper(new ArrayOfSimpleTypeClass()))->mapFromJson('{"arrayOfInt": [1,2,3,4]}')->arrayOfInt);
        $this->assertEquals(
            ['one' => [1, 2], 'two' => [3, 4]],
            (new ObjectMapper(new ArrayOfSimpleTypeClass()))->mapFromJson('{"arrayOfArrayOfInt": {"one":[1, 2], "two":[3, 4]}}')->arrayOfArrayOfInt
        );
        $this->assertEquals(
            ['one' => ['two' => [1, 2]], 'three' => ['four' => [3, 4]]],
            (new ObjectMapper(new ArrayOfSimpleTypeClass()))->mapFromJson('{"arrayOfArrayOfArrayOfInt": {"one":{"two":[1,2]}, "three":{"four":[3,4]}}}')->arrayOfArrayOfArrayOfInt
        );
    }

    public function test_BoxType()
    {
        $this->assertEquals([new DateTime('2022-01-01')], (new ObjectMapper(new ArrayOfBoxTypeClass()))->mapFromJson('{"arrayOfDate": ["2022-01-01"]}')->arrayOfDate);
        $this->assertEquals(
            ['one' => [new DateTime('2022-01-01')], 'two' => [new DateTime('2022-01-01')]],
            (new ObjectMapper(new ArrayOfBoxTypeClass()))->mapFromJson('{"arrayOfArrayOfDate": {"one":["2022-01-01"], "two":["2022-01-01"]}}')->arrayOfArrayOfDate
        );
        $this->assertEquals(
            ['one' => ['two' => [new DateTime('2022-01-01')]], 'three' => ['four' => [new DateTime('2022-01-01')]]],
            (new ObjectMapper(new ArrayOfBoxTypeClass()))->mapFromJson('{"arrayOfArrayOfArrayOfDate": {"one":{"two":["2022-01-01"]}, "three":{"four":["2022-01-01"]}}}')->arrayOfArrayOfArrayOfDate
        );
    }

    public function test_CustomType()
    {
        $customType      = new CustomTypeForArrayOf();
        $customType->key = 'value';

        $this->assertEquals([$customType], (new ObjectMapper(new ArrayOfCustomTypeClass()))->mapFromJson('{"arrayOfCustomType": [{"key":"value"}]}')->arrayOfCustomType);
        $this->assertEquals(
            ['one' => [$customType], 'two' => [$customType]],
            (new ObjectMapper(new ArrayOfCustomTypeClass()))->mapFromJson('{"arrayOfArrayOfCustomType": {"one":[{"key":"value"}], "two":[{"key":"value"}]}}')->arrayOfArrayOfCustomType
        );
        $this->assertEquals(
            ['one' => ['two' => [$customType]], 'three' => ['four' => [$customType]]],
            (new ObjectMapper(new ArrayOfCustomTypeClass()))->mapFromJson('{"arrayOfArrayOfArrayOfCustomType": {"one":{"two":[{"key":"value"}]}, "three":{"four":[{"key":"value"}]}}}')->arrayOfArrayOfArrayOfCustomType
        );
    }

    public function test_EnumType()
    {
        $this->assertEquals([EnumTypeForArrayOf::Hearts], (new ObjectMapper(new ArrayOfEnumTypeClass()))->mapFromJson('{"arrayOfEnumType": ["Hearts"]}')->arrayOfEnumType);
        $this->assertEquals(
            ['one' => [EnumTypeForArrayOf::Hearts], 'two' => [EnumTypeForArrayOf::Hearts]],
            (new ObjectMapper(new ArrayOfEnumTypeClass()))->mapFromJson('{"arrayOfArrayOfEnumType": {"one":["Hearts"], "two":["Hearts"]}}')->arrayOfArrayOfEnumType
        );
        $this->assertEquals(
            ['one' => ['two' => [EnumTypeForArrayOf::Hearts]], 'three' => ['four' => [EnumTypeForArrayOf::Hearts]]],
            (new ObjectMapper(new ArrayOfEnumTypeClass()))->mapFromJson('{"arrayOfArrayOfArrayOfEnumType": {"one":{"two":["Hearts"]}, "three":{"four":["Hearts"]}}}')->arrayOfArrayOfArrayOfEnumType
        );
    }
}
