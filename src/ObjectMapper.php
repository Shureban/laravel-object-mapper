<?php

namespace Shureban\LaravelObjectMapper;

use Illuminate\Foundation\Http\FormRequest;
use Shureban\LaravelObjectMapper\Attributes\SetterName;
use Shureban\LaravelObjectMapper\Exceptions\ParseJsonException;
use Shureban\LaravelObjectMapper\Exceptions\UnknownDataFormatException;

class ObjectMapper
{
    private object $result;

    /**
     * @param object $result
     */
    public function __construct(object $result)
    {
        $this->result = $result;
    }

    /**
     * @param string|array|FormRequest $data
     *
     * @return mixed
     * @throws ParseJsonException
     */
    public function map(string|array|FormRequest $data): object
    {
        return match (gettype($data)) {
            'string' => $this->mapFromJson($data),
            'array'  => $this->mapFromArray($data),
            'object' => $this->mapFromRequest($data),
            default  => new UnknownDataFormatException()
        };
    }

    /**
     * @param string $json
     *
     * @return mixed
     * @throws ParseJsonException
     */
    public function mapFromJson(string $json): object
    {
        $data  = json_decode($json, true);
        $error = json_last_error_msg();

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseJsonException($error);
        }

        return $this->mapData($data, $json);
    }

    /**
     * @param array                    $data
     * @param string|array|FormRequest $defaultData
     *
     * @return mixed
     */
    private function mapData(array $data, string|array|FormRequest $defaultData): object
    {
        $analyzer   = new ObjectAnalyzer($this->result);
        $properties = $analyzer->getProperties();

        /** @var Property $property */
        foreach ($properties as $property) {
            $objectPropertyName = $property->getObjectPropertyName();
            $dataPropertyName   = $property->getDataPropertyName();
            $setterName         = (string)new SetterName($objectPropertyName);

            if (!isset($data[$dataPropertyName])) {
                continue;
            }

            $value = $data[$dataPropertyName];

            if ($analyzer->hasSetter($setterName)) {
                call_user_func_array([$this->result, $setterName], [$value, $defaultData]);
                continue;
            }

            if ($property->isReadOnly()) {
                continue;
            }

            $this->result->{$objectPropertyName} = $property->convert($value);
        }

        return $this->result;
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function mapFromArray(array $data): object
    {
        return $this->mapData($data, $data);
    }

    /**
     * @param FormRequest $request
     * @param bool        $onlyValidated
     *
     * @return mixed
     */
    public function mapFromRequest(FormRequest $request, bool $onlyValidated = true): object
    {
        $data = $onlyValidated ? $request->validated() : $request->all();

        return $this->mapData($data, $request);
    }
}
