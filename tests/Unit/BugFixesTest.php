<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Carbon as SupportCarbon;
use Shureban\LaravelObjectMapper\Exceptions\InvalidValueTypeException;
use Shureban\LaravelObjectMapper\Exceptions\UnknownPropertyTypeException;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\PhpDoc;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\AbstractPropertyClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\BoxTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\MultilineDocClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SimpleTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\StaticMembersClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SupportCarbonClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\UnionWithOtherPropsClass;

class BugFixesTest extends TestCase
{
    public function test_multilineVarDocblockWithoutTrailingSpace()
    {
        $result = (new ObjectMapper(new MultilineDocClass()))->mapFromArray(['count' => '42']);

        $this->assertSame(42, $result->count);
    }

    public function test_nullableAndUnionPhpDocTypes()
    {
        $result = (new ObjectMapper(new MultilineDocClass()))->mapFromArray([
            'nullableName'  => 123,
            'unionWithNull' => '7',
        ]);

        $this->assertSame('123', $result->nullableName);
        $this->assertSame(7, $result->unionWithNull);
    }

    public function test_bracketsInDescriptionDoNotChangeNestingLevel()
    {
        $result = (new ObjectMapper(new MultilineDocClass()))->mapFromArray(['numbers' => ['1', '2']]);

        $this->assertSame([1, 2], $result->numbers);
    }

    public function test_phpDocNameIsNotStolenFromDescriptionText()
    {
        $phpDoc = new PhpDoc("/**\n * Description mentioning \$dollarWord.\n * @var int\n */");

        $this->assertNull($phpDoc->getPropertyName());
        $this->assertEquals('int', $phpDoc->getPropertyType());
    }

    public function test_supportCarbonPropertyIsMapped()
    {
        $result = (new ObjectMapper(new SupportCarbonClass()))->mapFromArray(['at' => '2024-01-01 10:00:00']);

        $this->assertInstanceOf(SupportCarbon::class, $result->at);
        $this->assertEquals('2024-01-01 10:00:00', $result->at->format('Y-m-d H:i:s'));
    }

    public function test_unixTimestampForCarbonAndDateTime()
    {
        $result = (new ObjectMapper(new BoxTypeClass()))->mapFromArray([
            'carbon'   => 1609459200,
            'dateTime' => 1609459200,
        ]);

        $this->assertInstanceOf(Carbon::class, $result->carbon);
        $this->assertInstanceOf(DateTime::class, $result->dateTime);
        $this->assertSame(1609459200, $result->carbon->getTimestamp());
        $this->assertSame(1609459200, $result->dateTime->getTimestamp());
    }

    public function test_unionTypedPropertyDoesNotBreakMappingOfOtherProperties()
    {
        $result = (new ObjectMapper(new UnionWithOtherPropsClass()))->mapFromArray(['name' => 'John']);

        $this->assertEquals('John', $result->name);
        $this->assertThrows(
            fn() => (new ObjectMapper(new UnionWithOtherPropsClass()))->mapFromArray(['id' => 10]),
            UnknownPropertyTypeException::class
        );
    }

    public function test_staticPropertiesAndSettersAreIgnored()
    {
        $result = (new ObjectMapper(new StaticMembersClass()))->mapFromArray([
            'name'               => 'John',
            'staticProperty'     => 'changed',
            'staticSetterTarget' => 'changed',
        ]);

        $this->assertEquals('John', $result->name);
        $this->assertEquals('initial', StaticMembersClass::$staticProperty);
        $this->assertEquals('initial', StaticMembersClass::$staticSetterTarget);
        $this->assertFalse(isset($result->staticSetterTarget));
    }

    public function test_boolTypeRecognizesCommonTruthyStrings()
    {
        $mapper = fn(mixed $value) => (new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['bool' => $value])->bool;

        $this->assertTrue($mapper('yes'));
        $this->assertTrue($mapper('on'));
        $this->assertTrue($mapper('TRUE'));
        $this->assertTrue($mapper('True'));
        $this->assertTrue($mapper(1.0));
        $this->assertFalse($mapper('no'));
        $this->assertFalse($mapper('off'));
        $this->assertFalse($mapper('0'));
        $this->assertFalse($mapper(2));
        $this->assertFalse($mapper('garbage'));
    }

    public function test_abstractClassPropertyThrowsInvalidValueType()
    {
        $this->assertThrows(
            fn() => (new ObjectMapper(new AbstractPropertyClass()))->mapFromArray(['target' => ['id' => 1]]),
            InvalidValueTypeException::class
        );
    }
}
