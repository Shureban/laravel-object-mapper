<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Carbon\Carbon;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Serializer;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\CollectionKeysDto;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\CustomTypeForArrayOf;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\MoneyWithToArray;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ObjectMetaDto;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SerializableClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\TestEnum;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\WalletDto;

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

    public function test_stdClassPropertyKeepsItsDynamicProperties()
    {
        $dto = (new ObjectMapper(new ObjectMetaDto()))->mapFromArray(['meta' => ['a' => 1, 'b' => 2]]);

        $this->assertSame(['meta' => ['a' => 1, 'b' => 2]], (new Serializer())->toArray($dto));
    }

    public function test_nestedObjectWithOwnToArrayIsDelegated()
    {
        $wallet        = new WalletDto();
        $wallet->money = MoneyWithToArray::fromAmount(5);

        $this->assertSame(['money' => ['amount' => 5]], (new Serializer())->toArray($wallet));
    }

    public function test_collectionKeepsStringKeys()
    {
        $dto        = new CollectionKeysDto();
        $dto->items = collect(['first' => 1, 'second' => 2]);

        $this->assertSame(['items' => ['first' => 1, 'second' => 2]], (new Serializer())->toArray($dto));
    }
}
