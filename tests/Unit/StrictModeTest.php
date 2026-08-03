<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\Exceptions\LossyConversionException;
use Shureban\LaravelObjectMapper\Exceptions\MappingFailedException;
use Shureban\LaravelObjectMapper\Exceptions\MissingRequiredValueException;
use Shureban\LaravelObjectMapper\Exceptions\UnknownDataKeyException;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\RequiredPropsClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SimpleTypeClass;

class StrictModeTest extends TestCase
{
    public function test_aggregatesAllErrorsInOneException()
    {
        try {
            (new ObjectMapper(new RequiredPropsClass()))->strict()->mapFromArray([
                'id'         => 'abc',
                'unknownKey' => 1,
            ]);
            $this->fail('Expected MappingFailedException');
        } catch (MappingFailedException $exception) {
            $errors = $exception->getErrors();

            $this->assertInstanceOf(LossyConversionException::class, $errors['id'][0]);
            $this->assertInstanceOf(UnknownDataKeyException::class, $errors['unknownKey'][0]);
        }
    }

    public function test_missingRequiredPropertyReported()
    {
        try {
            (new ObjectMapper(new RequiredPropsClass()))->strict()->mapFromArray([]);
            $this->fail('Expected MappingFailedException');
        } catch (MappingFailedException $exception) {
            $errors = $exception->getErrors();

            $this->assertInstanceOf(MissingRequiredValueException::class, $errors['id'][0]);
            $this->assertArrayNotHasKey('note', $errors);
            $this->assertArrayNotHasKey('title', $errors);
        }
    }

    public function test_strictBoolRejectsLooseTruthyStrings()
    {
        try {
            (new ObjectMapper(new SimpleTypeClass()))->strict()->mapFromArray(['bool' => 'yes']);
            $this->fail('Expected MappingFailedException');
        } catch (MappingFailedException $exception) {
            $this->assertInstanceOf(LossyConversionException::class, $exception->getErrors()['bool'][0]);
        }
    }

    public function test_strictWithValidDataMapsIdenticallyToNonStrict()
    {
        $data = ['id' => 10, 'note' => 'hello', 'title' => 'custom'];

        $strict = (new ObjectMapper(new RequiredPropsClass()))->strict()->mapFromArray($data);
        $loose  = (new ObjectMapper(new RequiredPropsClass()))->mapFromArray($data);

        $this->assertEquals($loose, $strict);
        $this->assertSame(10, $strict->id);
    }

    public function test_nonStrictKeepsLossyCoercions()
    {
        $result = (new ObjectMapper(new SimpleTypeClass()))->mapFromArray(['int' => 'abc']);

        $this->assertSame(0, $result->int);
    }
}
