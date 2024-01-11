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
     * @param array|object $data
     *
     * @return mixed
     */
    public function mapFromArray(array|object $data): object
    {
        $data = (array)$data;

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
     * Maps data to an object using the provided data and default data.
     *
     * @param array                    $data        The data to map.
     * @param string|array|FormRequest $defaultData The default data to use if a property's value is null.
     *
     * @return object  The mapped object.
     */
    private function mapData(array $data, string|array|FormRequest $defaultData): object
    {
        $analyzer   = new ObjectAnalyzer($this->result);
        $properties = $analyzer->getProperties();

        /** @var Property $property */
        foreach ($properties as $property) {
            $value = $this->getPropertyValue($property, $data);

            if ($value === null) {
                continue;
            }

            $objectPropertyName = $property->getObjectPropertyName();
            $setterName         = (string)new SetterName($property->getObjectPropertyName());

            if ($analyzer->hasSetter($setterName)) {
                call_user_func_array([$this->result, $setterName], [$value, $defaultData]);
                continue;
            }


            $this->result->{$objectPropertyName} = $property->convert($value);
        }

        return $this->result;
    }

    /**
     * Retrieve the value of a property from the given data array.
     *
     * @param Property $property The property for which to retrieve the value.
     * @param array    $data     The data array from which to retrieve the value.
     *
     * @return mixed|null The value of the property if it exists in the data array, otherwise null.
     */
    private function getPropertyValue(Property $property, array $data): mixed
    {
        if ($property->isReadOnly()) {
            return null;
        }

        $originalName     = $property->getOriginalName();
        $snakeCaseName    = $property->getSnakeCaseName();
        $otherCaseAllowed = config('object_mapper.snake_case_to_camel');

        return match (true) {
            isset($data[$originalName])                       => $data[$originalName],
            $otherCaseAllowed && isset($data[$snakeCaseName]) => $data[$snakeCaseName],
            default                                           => null,
        };
    }
}
