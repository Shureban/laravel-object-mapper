<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Illuminate\Foundation\Http\FormRequest;

class StubFormRequest extends FormRequest
{
    /**
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function validated($key = null, $default = null): mixed
    {
        return ['id' => 7, 'name' => 'John'];
    }
}
