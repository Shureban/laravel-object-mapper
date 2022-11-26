<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Illuminate\Foundation\Http\FormRequest;

class WithSetterClass
{
    public string $value;
    public string $request_value;

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = sprintf('setter_%s', $value);
    }

    /**
     * @param string      $value
     * @param FormRequest $request
     */
    public function setRequestValue(string $value, FormRequest $request): void
    {
        $this->request_value = sprintf('setter_%s', $value);
    }
}
