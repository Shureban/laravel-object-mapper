<?php

namespace Shureban\LaravelObjectMapper;

use Illuminate\Foundation\Http\FormRequest;
use ReflectionException;

trait MappableTrait
{
    /**
     * @param string|array|FormRequest $data
     *
     * @return $this
     * @throws Exceptions\UnknownPropertyTypeException
     * @throws ReflectionException
     */
    public function map(string|array|FormRequest $data): static
    {
        return (new ObjectMapper($this))->map($data);
    }

    /**
     * @param string $data
     *
     * @return $this
     * @throws Exceptions\UnknownPropertyTypeException
     * @throws ReflectionException
     */
    public function mapFromJson(string $data): static
    {
        return (new ObjectMapper($this))->mapFromJson($data);
    }

    /**
     * @param array $data
     *
     * @return $this
     * @throws Exceptions\UnknownPropertyTypeException
     * @throws ReflectionException
     */
    public function mapFromArray(array $data): static
    {
        return (new ObjectMapper($this))->mapFromArray($data);
    }

    /**
     * @param FormRequest $request
     * @param bool        $onlyValidated
     *
     * @return $this
     * @throws Exceptions\UnknownPropertyTypeException
     * @throws ReflectionException
     */
    public function mapFromRequest(FormRequest $request, bool $onlyValidated = true): static
    {
        return (new ObjectMapper($this))->mapFromRequest($request, $onlyValidated);
    }
}
