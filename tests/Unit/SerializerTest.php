<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Carbon\Carbon;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\CustomTypeForArrayOf;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SerializableClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\TestEnum;

class SerializerTest extends TestCase
{
    private function makeDto(): SerializableClass
    {
        $nested      = new CustomTypeForArrayOf();
        $nested->key = 'k';

        $dto                = new SerializableClass();
        $dto->id            = 7;
        $dto->suit          = TestEnum::Spades;
        $dto->birthday      = new Carbon('1991-12-31 10:00:00');
        $dto->nested        = $nested;

        return $dto;
    }

    public function test_toArrayUsesMappingKeysAndConvertsValues()
    {
        $result = $this->makeDto()->toArray();

        $this->assertSame([
            'user_id'       => 7,
            'suit'          => 'Spades',
            'birthday'      => '1991-12-31',
            'nested'        => ['key' => 'k'],
            'camelCaseName' => 'value',
        ], $result);
    }

    public function test_ignoredAndUninitializedPropertiesExcluded()
    {
        $result = $this->makeDto()->toArray();

        $this->assertArrayNotHasKey('secret', $result);
        $this->assertArrayNotHasKey('uninitialized', $result);
    }

    public function test_snakeCaseConfigAffectsUnmappedNames()
    {
        config()->set('object_mapper.serialize_snake_case', true);

        $result = $this->makeDto()->toArray();

        $this->assertArrayHasKey('camel_case_name', $result);
        $this->assertArrayHasKey('user_id', $result);
        $this->assertArrayNotHasKey('camelCaseName', $result);
    }

    public function test_toJson()
    {
        $json = $this->makeDto()->toJson();

        $this->assertSame(7, json_decode($json, true)['user_id']);
    }

    public function test_roundTrip()
    {
        $payload = ['user_id' => 1, 'suit' => 'Hearts', 'birthday' => '2000-01-01', 'nested' => ['key' => 'n'], 'camelCaseName' => 'x'];

        $dto = (new \Shureban\LaravelObjectMapper\ObjectMapper(new SerializableClass()))->mapFromArray($payload);

        $this->assertSame($payload, $dto->toArray());
    }
}
