<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Illuminate\Foundation\Http\FormRequest;

class WithSetterClass
{
    /** @var string $from_json_value */
    public string $fromJsonValue;
    public string $fromArrayValue;
    public string $from_request_value;

    /**
     * @param string $value
     * @param string $json
     */
    public function setFromJsonValue(string $value, string $json): void
    {
        $this->fromJsonValue = sprintf('setter_%s', $value);
    }

    /**
     * @param string $value
     * @param array  $data
     */
    public function setFromArrayValue(string $value, array $data): void
    {
        $this->fromArrayValue = sprintf('setter_%s', $value);
    }

    /**
     * @param string      $value
     * @param FormRequest $request
     */
    public function setFromRequestValue(string $value, FormRequest $request): void
    {
        $this->from_request_value = sprintf('setter_%s', $value);
    }
}
