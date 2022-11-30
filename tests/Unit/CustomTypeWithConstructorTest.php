<?php

namespace Shureban\LaravelObjectMapper\Tests\Structs;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\CustomCorrectOneEmptyTypeParameterConstructorTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\CustomCorrectOneIntTypeParameterConstructorTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\CustomCorrectOneMixedTypeParameterConstructorTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\CustomTypeWithConstructorClass;
use Tests\TestCase;

class CustomTypeWithConstructorTest extends TestCase
{
    public function test_WithCorrectOneParameterConstructor()
    {
        $this->assertEquals(
            new CustomCorrectOneEmptyTypeParameterConstructorTypeClass(10),
            (new ObjectMapper(new CustomTypeWithConstructorClass()))->mapFromJson('{"emptyTypeOne": 10}')->emptyTypeOne
        );
        $this->assertEquals(
            new CustomCorrectOneMixedTypeParameterConstructorTypeClass(10),
            (new ObjectMapper(new CustomTypeWithConstructorClass()))->mapFromJson('{"mixedTypeOne": 10}')->mixedTypeOne
        );
        $this->assertEquals(
            new CustomCorrectOneIntTypeParameterConstructorTypeClass(10),
            (new ObjectMapper(new CustomTypeWithConstructorClass()))->mapFromJson('{"intTypeOne": 10}')->intTypeOne
        );
    }
}
