<?php

namespace Shureban\LaravelObjectMapper\Tests\Structs;

use Shureban\LaravelObjectMapper\Exceptions\ParseJsonException;
use Shureban\LaravelObjectMapper\Exceptions\WrongConstructorParametersNumberException;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\CustomTypeWithConstructorClass;
use stdClass;
use Tests\TestCase;

class ErrorsTest extends TestCase
{
    public function test_ParseJsonError()
    {
        $this->assertThrows(fn() => (new ObjectMapper(new stdClass()))->mapFromJson('{"int": 10"}'), ParseJsonException::class);
    }

    public function test_WithTwoRequiredParametersConstructor()
    {
        $this->assertThrows(fn() => (new ObjectMapper(new CustomTypeWithConstructorClass()))->mapFromJson('{"twoRequired": 10}'), WrongConstructorParametersNumberException::class);
    }
}
