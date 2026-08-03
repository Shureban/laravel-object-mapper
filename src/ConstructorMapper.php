<?php

namespace Shureban\LaravelObjectMapper;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionParameter;
use Shureban\LaravelObjectMapper\Attributes\MapFrom;
use Shureban\LaravelObjectMapper\Exceptions\MissingConstructorValueException;
use Shureban\LaravelObjectMapper\Support\ClassMetadata;
use Shureban\LaravelObjectMapper\Types\Factory;

class ConstructorMapper
{
    private string $className;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * Builds an instance by resolving every constructor parameter from the data array.
     * Parameters absent from the data fall back to their default value or null (when nullable);
     * unresolvable required parameters are reported all at once.
     *
     * @param array $data
     *
     * @return object
     * @throws MissingConstructorValueException
     */
    public function map(array $data): object
    {
        $reflection  = ClassMetadata::for($this->className)->getReflection();
        $constructor = $reflection->getConstructor();

        if (is_null($constructor) || $constructor->getNumberOfParameters() === 0) {
            return new $this->className();
        }

        $arguments = [];
        $missing   = [];

        foreach ($constructor->getParameters() as $parameter) {
            $value = $this->getParameterValue($parameter, $data);

            if ($value === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $arguments[] = $parameter->getDefaultValue();
                    continue;
                }

                if ($parameter->allowsNull()) {
                    $arguments[] = null;
                    continue;
                }

                $missing[] = $parameter->getName();
                continue;
            }

            $arguments[] = Factory::makeForParameter($parameter)->convert($value);
        }

        if ($missing !== []) {
            throw new MissingConstructorValueException($this->className, $missing);
        }

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * Top-level data keys consumed by the constructor parameters — used by strict mode
     * to tell real unknown keys from constructor input.
     *
     * @param array $data
     *
     * @return array|string[]
     */
    public function consumedKeys(array $data): array
    {
        $reflection  = ClassMetadata::for($this->className)->getReflection();
        $constructor = $reflection->getConstructor();

        if (is_null($constructor)) {
            return [];
        }

        $consumed = [];

        foreach ($constructor->getParameters() as $parameter) {
            $attributes   = $parameter->getAttributes(MapFrom::class);
            $originalName = $attributes === [] ? $parameter->getName() : $attributes[0]->newInstance()->key;
            $topLevelName = str_contains($originalName, '.') ? explode('.', $originalName)[0] : $originalName;

            if (array_key_exists($topLevelName, $data)) {
                $consumed[] = $topLevelName;
                continue;
            }

            $snakeCaseName = Str::snake($topLevelName);

            if (config('object_mapper.snake_case_to_camel') && array_key_exists($snakeCaseName, $data)) {
                $consumed[] = $snakeCaseName;
            }
        }

        return $consumed;
    }

    /**
     * @param ReflectionParameter $parameter
     * @param array               $data
     *
     * @return mixed
     */
    private function getParameterValue(ReflectionParameter $parameter, array $data): mixed
    {
        $attributes   = $parameter->getAttributes(MapFrom::class);
        $originalName = $attributes === [] ? $parameter->getName() : $attributes[0]->newInstance()->key;

        if (str_contains($originalName, '.')) {
            return Arr::get($data, $originalName);
        }

        $snakeCaseName    = Str::snake($originalName);
        $otherCaseAllowed = config('object_mapper.snake_case_to_camel');

        return match (true) {
            isset($data[$originalName])                       => $data[$originalName],
            $otherCaseAllowed && isset($data[$snakeCaseName]) => $data[$snakeCaseName],
            default                                           => null,
        };
    }
}
