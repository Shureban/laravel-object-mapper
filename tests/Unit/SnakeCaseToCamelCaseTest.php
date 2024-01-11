<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Illuminate\Foundation\Http\FormRequest;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ClassWithCamelCaseValues;
use Tests\TestCase;

class SnakeCaseToCamelCaseTest extends TestCase
{
    public function test_handleSnakeCaseLikeCamelCase()
    {
        $request = new FormRequest();
        $request->merge(['from_request_int' => '12345']);
        $request->merge(['from_request_string' => 'test']);
        $request->merge(['from_request_bool' => 'true']);

        /** @var ClassWithCamelCaseValues $result */
        $result = (new ObjectMapper(new ClassWithCamelCaseValues()))->mapFromRequest($request, false);

        $this->assertEquals(12345, $result->fromRequestInt);
        $this->assertEquals('test', $result->fromRequestString);
        $this->assertEquals(true, $result->fromRequestBool);
        $this->assertEquals(null, $result->fromRequestNull);
    }
}
