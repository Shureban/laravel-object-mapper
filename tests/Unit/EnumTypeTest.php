<?php

namespace Shureban\LaravelObjectMapper\Tests\Structs;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\EnumTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\TestEnum;
use Tests\TestCase;

class EnumTypeTest extends TestCase
{
    public function test_Enum()
    {
        $this->assertEquals(TestEnum::Hearts, (new ObjectMapper(new EnumTypeClass()))->mapFromJson('{"strictEnum": "Hearts"}')->strictEnum);
        $this->assertEquals(TestEnum::Hearts, (new ObjectMapper(new EnumTypeClass()))->mapFromJson('{"phpDocEnum": "Hearts"}')->phpDocEnum);
        $this->assertEquals(TestEnum::Hearts, (new ObjectMapper(new EnumTypeClass()))->mapFromJson('{"bothEnum": "Hearts"}')->bothEnum);
    }
}
