<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\Exceptions\ObjectMapperException;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\NestedReadonlyHolderClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ValueObjectHolderClass;

class ValueObjectFactoryTest extends TestCase
{
    public function test_scalarValueGoesThroughStaticFromFactory()
    {
        $result = (new ObjectMapper(new ValueObjectHolderClass()))->mapFromArray(['email' => 'USER@Example.COM']);

        $this->assertSame('user@example.com', $result->email->value);
    }

    public function test_classesWithoutFromFactoryKeepConstructorBehavior()
    {
        $result = (new ObjectMapper(new ValueObjectHolderClass()))->mapFromArray(['viaConstructor' => 42]);

        $this->assertSame(42, $result->viaConstructor->id);
    }

    public function test_singleArgumentConstructorStillWinsOverFromFactory()
    {
        $result = (new ObjectMapper(new NestedReadonlyHolderClass()))->mapFromArray(['price' => '10.50']);

        $this->assertSame('10.50', $result->price->raw);
    }

    public function test_incompatiblePayloadForValueObjectThrowsObjectMapperException()
    {
        $this->assertThrows(
            fn() => (new ObjectMapper(new ValueObjectHolderClass()))->mapFromArray(['email' => ['value' => 'a@b.c']]),
            ObjectMapperException::class
        );
    }

    public function test_nestedDtoWithRequiredConstructorParamsIsBuiltViaConstructor()
    {
        $result = (new ObjectMapper(new NestedReadonlyHolderClass()))->mapFromArray([
            'inner' => ['id' => 5, 'full_name' => 'John'],
        ]);

        $this->assertSame(5, $result->inner->id);
        $this->assertSame('John', $result->inner->name);
    }
}
