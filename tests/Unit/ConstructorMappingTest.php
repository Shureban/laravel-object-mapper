<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\Exceptions\MissingConstructorValueException;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\DottedCtorDtoClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\IgnoredPromotedDtoClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ReadonlyDtoClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SimpleTypeClass;

class ConstructorMappingTest extends TestCase
{
    public function test_fromBuildsReadonlyDtoWithTypeConversion()
    {
        $dto = ReadonlyDtoClass::from(['id' => '5', 'full_name' => 'John']);

        $this->assertSame(5, $dto->id);
        $this->assertSame('John', $dto->name);
        $this->assertSame('user', $dto->role);
    }

    public function test_fromAcceptsJson()
    {
        $dto = ReadonlyDtoClass::from('{"id": 1, "full_name": "A", "role": "admin"}');

        $this->assertSame(1, $dto->id);
        $this->assertSame('admin', $dto->role);
    }

    public function test_classStringMapperMode()
    {
        $dto = (new ObjectMapper(ReadonlyDtoClass::class))->mapFromJson('{"id": 1, "full_name": "A"}');

        $this->assertInstanceOf(ReadonlyDtoClass::class, $dto);
        $this->assertSame('A', $dto->name);
    }

    public function test_snakeCaseFallbackWorksForConstructorParams()
    {
        $dto = ReadonlyDtoClass::from(['id' => 2, 'full_name' => 'B', 'role' => 'admin']);

        $this->assertSame('admin', $dto->role);
    }

    public function test_missingRequiredParamsThrowWithAllNames()
    {
        try {
            ReadonlyDtoClass::from(['role' => 'admin']);
            $this->fail('Expected MissingConstructorValueException');
        } catch (MissingConstructorValueException $exception) {
            $this->assertStringContainsString('id', $exception->getMessage());
            $this->assertStringContainsString('name', $exception->getMessage());
        }
    }

    public function test_classStringWithoutConstructorBehavesLikeInstanceMode()
    {
        $result = (new ObjectMapper(SimpleTypeClass::class))->mapFromArray(['int' => 10]);

        $this->assertInstanceOf(SimpleTypeClass::class, $result);
        $this->assertSame(10, $result->int);
    }

    public function test_ignoredPromotedPropertyIsNeverFilledFromData()
    {
        $dto = IgnoredPromotedDtoClass::from(['email' => 'a@b.c', 'isAdmin' => true]);

        $this->assertSame('a@b.c', $dto->email);
        $this->assertFalse($dto->isAdmin);
    }

    public function test_dottedMapFromParamFallsBackToSnakeCaseSegments()
    {
        $dto = DottedCtorDtoClass::from(['user_profile' => ['first_name' => 'Ann']]);

        $this->assertSame('Ann', $dto->firstName);
    }

    public function test_reusedClassStringMapperBuildsFreshInstancePerCall()
    {
        $mapper = new ObjectMapper(ReadonlyDtoClass::class);

        $first  = $mapper->mapFromArray(['id' => 1, 'full_name' => 'first']);
        $second = $mapper->mapFromArray(['id' => 2, 'full_name' => 'second']);

        $this->assertNotSame($first, $second);
        $this->assertSame('first', $first->name);
        $this->assertSame('second', $second->name);
    }
}
