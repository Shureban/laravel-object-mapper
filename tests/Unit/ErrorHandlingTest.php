<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Carbon\Carbon;
use DateTime;
use Shureban\LaravelObjectMapper\Exceptions\InvalidDateTimeValueException;
use Shureban\LaravelObjectMapper\Exceptions\InvalidEnumValueException;
use Shureban\LaravelObjectMapper\Exceptions\InvalidJsonStructureException;
use Shureban\LaravelObjectMapper\Exceptions\InvalidModelKeyException;
use Shureban\LaravelObjectMapper\Exceptions\InvalidValueTypeException;
use Shureban\LaravelObjectMapper\Exceptions\MissingConstructorValueException;
use Shureban\LaravelObjectMapper\Exceptions\ParseJsonException;
use Shureban\LaravelObjectMapper\Exceptions\UnknownPropertyTypeException;
use Shureban\LaravelObjectMapper\Exceptions\WrongConstructorParametersNumberException;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\BoxTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\CustomCorrectOneIntTypeParameterConstructorTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\CustomTypeWithConstructorClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\EnumTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ModelTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\NullFindModelClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\PrivateSetterClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\PureEnumClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SimpleTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SomeModel;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\TestEnum;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\UnionTypeClass;
use stdClass;
use Stringable;

class ErrorHandlingTest extends TestCase
{
    public function test_jsonDecodingToNonArrayThrowsInvalidJsonStructure()
    {
        $mapper = fn() => new ObjectMapper(new stdClass());

        $this->assertThrows(fn() => $mapper()->mapFromJson('null'), InvalidJsonStructureException::class);
        $this->assertThrows(fn() => $mapper()->mapFromJson('123'), InvalidJsonStructureException::class);
        $this->assertThrows(fn() => $mapper()->mapFromJson('"string"'), InvalidJsonStructureException::class);
        $this->assertThrows(fn() => $mapper()->mapFromJson('true'), InvalidJsonStructureException::class);
    }

    public function test_brokenJsonThrowsParseJsonException()
    {
        $this->assertThrows(fn() => (new ObjectMapper(new stdClass()))->mapFromJson('{"int": 10"}'), ParseJsonException::class);
        $this->assertThrows(fn() => (new ObjectMapper(new stdClass()))->map('{"int": 10"}'), ParseJsonException::class);
    }

    public function test_emptyJsonArrayIsAllowed()
    {
        $result = (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('[]');

        $this->assertInstanceOf(SimpleTypeClass::class, $result);
    }

    public function test_nullValueInDataIsSkipped()
    {
        $result = (new ObjectMapper(new SimpleTypeClass()))->mapFromJson('{"int": null}');

        $this->assertFalse(isset($result->int));
    }

    public function test_emptyStringDateThrowsInvalidDateTimeValue()
    {
        $this->assertThrows(fn() => (new ObjectMapper(new BoxTypeClass()))->mapFromArray(['carbon' => '']), InvalidDateTimeValueException::class);
        $this->assertThrows(fn() => (new ObjectMapper(new BoxTypeClass()))->mapFromArray(['dateTime' => '']), InvalidDateTimeValueException::class);
    }

    public function test_garbageDateThrowsInvalidDateTimeValue()
    {
        $this->assertThrows(fn() => (new ObjectMapper(new BoxTypeClass()))->mapFromArray(['carbon' => 'not-a-date']), InvalidDateTimeValueException::class);
        $this->assertThrows(fn() => (new ObjectMapper(new BoxTypeClass()))->mapFromArray(['dateTime' => 'not-a-date']), InvalidDateTimeValueException::class);
        $this->assertThrows(fn() => (new ObjectMapper(new BoxTypeClass()))->mapFromArray(['carbon' => ['nested' => 'array']]), InvalidDateTimeValueException::class);
        $this->assertThrows(fn() => (new ObjectMapper(new BoxTypeClass()))->mapFromArray(['dateTime' => false]), InvalidDateTimeValueException::class);
    }

    public function test_dateTimeInstancePassesThrough()
    {
        $dateTime = new DateTime('2021-05-10 10:00:00');
        $result   = (new ObjectMapper(new BoxTypeClass()))->mapFromArray(['dateTime' => $dateTime, 'carbon' => $dateTime]);

        $this->assertSame($dateTime, $result->dateTime);
        $this->assertInstanceOf(Carbon::class, $result->carbon);
        $this->assertEquals('2021-05-10 10:00:00', $result->carbon->format('Y-m-d H:i:s'));
    }

    public function test_invalidEnumValueThrowsInvalidEnumValue()
    {
        $this->assertThrows(fn() => (new ObjectMapper(new EnumTypeClass()))->mapFromArray(['strictEnum' => 'NotACard']), InvalidEnumValueException::class);
        $this->assertThrows(fn() => (new ObjectMapper(new EnumTypeClass()))->mapFromArray(['strictEnum' => ['array']]), InvalidEnumValueException::class);
    }

    public function test_pureEnumThrowsInvalidEnumValue()
    {
        $this->assertThrows(fn() => (new ObjectMapper(new PureEnumClass()))->mapFromArray(['pureEnum' => 'First']), InvalidEnumValueException::class);
    }

    public function test_enumInstancePassesThrough()
    {
        $result = (new ObjectMapper(new EnumTypeClass()))->mapFromArray(['strictEnum' => TestEnum::Hearts]);

        $this->assertSame(TestEnum::Hearts, $result->strictEnum);
    }

    public function test_invalidModelKeyThrowsInvalidModelKey()
    {
        config()->set('object_mapper.implicit_model_lookup', true);

        $this->assertThrows(fn() => (new ObjectMapper(new ModelTypeClass()))->mapFromArray(['model_id' => [1, 2]]), InvalidModelKeyException::class);
        $this->assertThrows(fn() => (new ObjectMapper(new ModelTypeClass()))->mapFromArray(['model_id' => true]), InvalidModelKeyException::class);
    }

    public function test_modelInstancePassesThrough()
    {
        config()->set('object_mapper.implicit_model_lookup', true);

        $model  = new SomeModel(5);
        $result = (new ObjectMapper(new ModelTypeClass()))->mapFromArray(['model_id' => $model]);

        $this->assertSame($model, $result->phpDocModel);
    }

    public function test_notFoundModelLeavesPropertyUntouched()
    {
        config()->set('object_mapper.implicit_model_lookup', true);

        $result = (new ObjectMapper(new NullFindModelClass()))->mapFromArray(['model' => 99]);

        $this->assertFalse(isset($result->model));
    }

    public function test_arrayValueForConstructorWithRequiredParametersIsBuiltViaConstructor()
    {
        $result = (new ObjectMapper(new CustomTypeWithConstructorClass()))->mapFromArray(['intTypeOne' => ['id' => 1]]);

        $this->assertSame(1, $result->intTypeOne->id);
    }

    public function test_arrayValueMissingConstructorKeysThrows()
    {
        $this->assertThrows(
            fn() => (new ObjectMapper(new CustomTypeWithConstructorClass()))->mapFromArray(['intTypeOne' => ['bogus' => 1]]),
            MissingConstructorValueException::class
        );
    }

    public function test_customTypeInstancePassesThrough()
    {
        $custom = new CustomCorrectOneIntTypeParameterConstructorTypeClass(7);
        $result = (new ObjectMapper(new CustomTypeWithConstructorClass()))->mapFromArray(['intTypeOne' => $custom]);

        $this->assertSame($custom, $result->intTypeOne);
    }

    public function test_arrayIntoScalarPropertyThrowsInvalidValueType()
    {
        $this->assertThrows(fn() => (new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['string' => ['a']]), InvalidValueTypeException::class);
        $this->assertThrows(fn() => (new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['int' => ['a']]), InvalidValueTypeException::class);
        $this->assertThrows(fn() => (new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['float' => ['a']]), InvalidValueTypeException::class);
    }

    public function test_stringablePassesIntoStringProperty()
    {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'stringable_value';
            }
        };

        $result = (new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['string' => $stringable]);

        $this->assertEquals('stringable_value', $result->string);
    }

    public function test_unionTypePropertyThrowsUnknownPropertyType()
    {
        $this->assertThrows(fn() => (new ObjectMapper(new UnionTypeClass()))->mapFromArray(['value' => 10]), UnknownPropertyTypeException::class);
    }

    public function test_privateSetterIsIgnoredAndValueAssignedDirectly()
    {
        $result = (new ObjectMapper(new PrivateSetterClass()))->mapFromArray(['name' => 'John']);

        $this->assertEquals('John', $result->name);
    }
}
