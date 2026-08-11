<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\Exceptions\InvalidJsonStructureException;
use Shureban\LaravelObjectMapper\Exceptions\InvalidValueTypeException;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ReadonlyDtoClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SimpleTypeClass;

class MapArrayOfTest extends TestCase
{
    public function test_mapsJsonListIntoTypedInstances()
    {
        $result = ObjectMapper::mapArrayOf(SimpleTypeClass::class, '[{"int": 1}, {"int": 2}]');

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(SimpleTypeClass::class, $result);
        $this->assertSame(1, $result[0]->int);
        $this->assertSame(2, $result[1]->int);
    }

    public function test_mapsPhpArrayList()
    {
        $result = ObjectMapper::mapArrayOf(SimpleTypeClass::class, [['string' => 'a'], ['string' => 'b']]);

        $this->assertSame('b', $result[1]->string);
    }

    public function test_readonlyDtoList()
    {
        $result = ObjectMapper::mapArrayOf(ReadonlyDtoClass::class, [
            ['id' => 1, 'full_name' => 'A'],
            ['id' => 2, 'full_name' => 'B', 'role' => 'admin'],
        ]);

        $this->assertSame('admin', $result[1]->role);
        $this->assertSame(1, $result[0]->id);
    }

    public function test_mapOfKeysIsRejected()
    {
        $this->assertThrows(fn() => ObjectMapper::mapArrayOf(SimpleTypeClass::class, '{"a": 1}'), InvalidJsonStructureException::class);
        $this->assertThrows(fn() => ObjectMapper::mapArrayOf(SimpleTypeClass::class, 'null'), InvalidJsonStructureException::class);
    }

    public function test_scalarItemsAreRejected()
    {
        $this->assertThrows(fn() => ObjectMapper::mapArrayOf(SimpleTypeClass::class, [1, 2]), InvalidValueTypeException::class);
    }

    public function test_emptyListGivesEmptyArray()
    {
        $this->assertSame([], ObjectMapper::mapArrayOf(SimpleTypeClass::class, '[]'));
    }

    public function test_traitFromMany()
    {
        $result = ReadonlyDtoClass::fromMany('[{"id": 1, "full_name": "A"}]');

        $this->assertCount(1, $result);
        $this->assertSame('A', $result[0]->name);
    }
}
