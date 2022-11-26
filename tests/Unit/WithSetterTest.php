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
        $request->merge(['request_value' => 'request_value']);

        $this->assertEquals('setter_value', (new ObjectMapper(new WithSetterClass()))->mapFromJson('{"value": "value"}')->value);
        $this->assertEquals('setter_request_value', (new ObjectMapper(new WithSetterClass()))->mapFromRequest($request, false)->request_value);
    }
}
