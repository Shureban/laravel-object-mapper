<?php

namespace Shureban\LaravelObjectMapper;

use Illuminate\Foundation\Http\FormRequest;
use Shureban\LaravelObjectMapper\Attributes\SetterName;
use Shureban\LaravelObjectMapper\Exceptions\UnknownDataFormatException;
use Shureban\LaravelObjectMapper\Types\CustomObjectTypeInterface;

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
     * @return object
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
     * @param string $data
     *
     * @return object
     */
    public function mapFromJson(string $data): object
    {
        $data = json_decode($data, true);

        return $this->mapFromArray($data);
    }

    /**
     * @param array $data
     *
     * @return object
     */
    public function mapFromArray(array $data): object
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
                call_user_func_array([$this->result, $setterName], [$value, $data]);
                continue;
            }

            $convertedValue = $property->convert($value);

            if ($property->getType() instanceof CustomObjectTypeInterface) {
                $convertedValue = (new ObjectMapper($convertedValue))->mapFromArray($value);
            }

            $this->result->{$objectPropertyName} = $convertedValue;
        }

        return $this->result;
    }

    /**
     * @param FormRequest $request
     * @param bool        $onlyValidated
     *
     * @return object
     */
    public function mapFromRequest(FormRequest $request, bool $onlyValidated = true): object
    {
        $data = $onlyValidated ? $request->validated() : $request->all();

        return $this->mapFromArray($data);
    }
}
