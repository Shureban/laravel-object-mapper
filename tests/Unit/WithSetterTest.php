<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Illuminate\Foundation\Http\FormRequest;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\WithSetterClass;
use Tests\TestCase;

class WithSetterTest extends TestCase
{
    public function test_withSetter()
    {
        $request = new FormRequest();
        $request->merge(['from_request_value' => 'from_request_value']);

        $this->assertEquals('setter_from_json_value', (new ObjectMapper(new WithSetterClass()))->mapFromJson('{"from_json_value": "from_json_value"}')->fromJsonValue);
        $this->assertEquals('setter_fromArrayValue', (new ObjectMapper(new WithSetterClass()))->mapFromArray(['fromArrayValue' => 'fromArrayValue'])->fromArrayValue);
        $this->assertEquals('setter_from_request_value', (new ObjectMapper(new WithSetterClass()))->mapFromRequest($request, false)->from_request_value);
    }
}
