<?php

namespace Shureban\LaravelObjectMapper;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Shureban\LaravelObjectMapper\Exceptions\InvalidJsonStructureException;
use Shureban\LaravelObjectMapper\Exceptions\InvalidValueTypeException;
use Shureban\LaravelObjectMapper\Exceptions\MappingFailedException;
use Shureban\LaravelObjectMapper\Exceptions\MissingConstructorValueException;
use Shureban\LaravelObjectMapper\Exceptions\MissingRequiredValueException;
use Shureban\LaravelObjectMapper\Exceptions\ObjectMapperException;
use Shureban\LaravelObjectMapper\Exceptions\ParseJsonException;
use Shureban\LaravelObjectMapper\Exceptions\UnknownDataFormatException;
use Shureban\LaravelObjectMapper\Exceptions\UnknownDataKeyException;
use Shureban\LaravelObjectMapper\Support\ClassMetadata;
use Shureban\LaravelObjectMapper\Support\KeyPath;
use Shureban\LaravelObjectMapper\Support\SetterName;
use Shureban\LaravelObjectMapper\Support\StrictChecks;

class ObjectMapper
{
    /**
     * Strict mode of the mapping currently in progress. Types stay strict-agnostic:
     * nested mappers created inside them (CustomType, ArrayOf items) inherit
     * strictness from this context instead of a constructor argument.
     */
    private static bool $strictContext = false;

    private object|string $result;
    private ?string       $constructorClass = null;
    private bool          $strict           = false;

    /**
     * @param object|class-string $result A target instance, or a class name — the instance is then
     *                                    built via constructor mapping (readonly DTO support).
     */
    public function __construct(object|string $result)
    {
        $this->result = $result;
    }

    /**
     * Enables strict mode: unknown data keys, lossy scalar coercions and missing required
     * properties are collected and reported together via MappingFailedException.
     *
     * @param bool $strict
     *
     * @return $this
     */
    public function strict(bool $strict = true): self
    {
        $this->strict = $strict;

        return $this;
    }

    /**
     * Maps a JSON list (or a PHP list of arrays) into an array of $class instances.
     *
     * @template T of object
     *
     * @param class-string<T> $class
     * @param string|array    $data
     *
     * @return array|T[]
     * @throws ParseJsonException
     * @throws InvalidJsonStructureException
     * @throws InvalidValueTypeException
     */
    public static function mapArrayOf(string $class, string|array $data): array
    {
        if (is_string($data)) {
            $decoded = json_decode($data, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ParseJsonException(json_last_error_msg());
            }

            if (!is_array($decoded)) {
                throw new InvalidJsonStructureException(get_debug_type($decoded));
            }

            $data = $decoded;
        }

        if (!array_is_list($data)) {
            throw new InvalidJsonStructureException('map of keys');
        }

        return array_map(function (mixed $item) use ($class) {
            if (!is_array($item) && !is_object($item)) {
                throw new InvalidValueTypeException($class, $item);
            }

            return (new self($class))->mapFromArray($item);
        }, $data);
    }

    /**
     * @param string|array|FormRequest $data
     *
     * @return object
     * @throws ParseJsonException
     * @throws InvalidJsonStructureException
     * @throws UnknownDataFormatException
     */
    public function map(string|array|FormRequest $data): object
    {
        return match (true) {
            is_string($data)             => $this->mapFromJson($data),
            is_array($data)              => $this->mapFromArray($data),
            $data instanceof FormRequest => $this->mapFromRequest($data),
            default                      => throw new UnknownDataFormatException()
        };
    }

    /**
     * @param array|object $data
     *
     * @return object
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
     * @return object
     */
    public function mapFromRequest(FormRequest $request, bool $onlyValidated = true): object
    {
        $data = $onlyValidated ? $request->validated() : $request->all();

        return $this->mapData($data, $request);
    }

    /**
     * @param string $json
     *
     * @return object
     * @throws ParseJsonException
     * @throws InvalidJsonStructureException
     */
    public function mapFromJson(string $json): object
    {
        $data  = json_decode($json, true);
        $error = json_last_error_msg();

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParseJsonException($error);
        }

        if (!is_array($data)) {
            throw new InvalidJsonStructureException(get_debug_type($data));
        }

        return $this->mapData($data, $json);
    }

    /**
     * Maps data to an object using the provided data and default data.
     *
     * @param array                    $data        The data to map.
     * @param string|array|FormRequest $defaultData The raw source passed to setter methods.
     *
     * @return object  The mapped object.
     * @throws ObjectMapperException
     */
    private function mapData(array $data, string|array|FormRequest $defaultData): object
    {
        $this->strict    = $this->strict || self::$strictContext;
        $previousContext = self::$strictContext;

        self::$strictContext = $this->strict;

        try {
            return $this->doMapData($data, $defaultData);
        } finally {
            self::$strictContext = $previousContext;
        }
    }

    /**
     * @param array                    $data
     * @param string|array|FormRequest $defaultData
     *
     * @return object
     * @throws ObjectMapperException
     */
    private function doMapData(array $data, string|array|FormRequest $defaultData): object
    {
        $target = $this->result;

        if (is_string($target)) {
            $this->constructorClass = $target;
            $target                 = $this->buildViaConstructor($target, $data);
        }

        $analyzer   = new ObjectAnalyzer($target);
        $properties = $analyzer->getProperties();
        $errors     = [];

        /** @var Property $property */
        foreach ($properties as $property) {
            try {
                $this->mapProperty($target, $property, $analyzer, $data, $defaultData);
            } catch (ObjectMapperException $exception) {
                if (!$this->strict) {
                    throw $exception;
                }

                $errors[$property->getObjectPropertyName()][] = $exception;
            }
        }

        if ($this->strict) {
            $this->collectUnknownKeys($properties, $data, $errors);
            $this->collectMissingRequired($target, $properties, $errors);

            if ($errors !== []) {
                throw new MappingFailedException($errors);
            }
        }

        return $target;
    }

    /**
     * Constructor-mode instantiation. In strict mode a missing-values failure is
     * reported the same way as property errors: aggregated into MappingFailedException
     * together with the unknown data keys.
     *
     * @param class-string $class
     * @param array        $data
     *
     * @return object
     * @throws ObjectMapperException
     */
    private function buildViaConstructor(string $class, array $data): object
    {
        try {
            return (new ConstructorMapper($class))->map($data);
        } catch (MissingConstructorValueException $exception) {
            if (!$this->strict) {
                throw $exception;
            }

            $errors = [];

            foreach ($exception->getParameters() as $parameterName) {
                $errors[$parameterName][] = new MissingRequiredValueException($parameterName);
            }

            $this->collectUnknownKeys(ClassMetadata::for($class)->getProperties(), $data, $errors);

            throw new MappingFailedException($errors);
        }
    }

    /**
     * @param object                   $target
     * @param Property                 $property
     * @param ObjectAnalyzer           $analyzer
     * @param array                    $data
     * @param string|array|FormRequest $defaultData
     *
     * @return void
     * @throws ObjectMapperException
     */
    private function mapProperty(object $target, Property $property, ObjectAnalyzer $analyzer, array $data, string|array|FormRequest $defaultData): void
    {
        $value = $this->getPropertyValue($property, $data);

        if ($value === null) {
            $assignNullAllowed = config('object_mapper.assign_explicit_null') === true;

            if ($assignNullAllowed && $property->isNullable() && !$property->isReadOnly() && !$property->isIgnored() && $this->hasExplicitNull($property, $data)) {
                $target->{$property->getObjectPropertyName()} = null;
            }

            return;
        }

        $objectPropertyName = $property->getObjectPropertyName();
        $setterName         = (string)new SetterName($objectPropertyName);

        if ($analyzer->hasSetter($setterName)) {
            call_user_func_array([$target, $setterName], [$value, $defaultData]);

            return;
        }

        if ($this->strict) {
            StrictChecks::validate($property->getType(), $value);
        }

        $convertedValue = $property->convert($value);

        // A converter may legitimately map a present value to null (custom types);
        // non-nullable properties keep their default instead (e.g. Eloquent lookup miss).
        if ($convertedValue === null && !$property->isNullable()) {
            return;
        }

        $target->{$objectPropertyName} = $convertedValue;
    }

    /**
     * @param array|Property[]                        $properties
     * @param array                                   $data
     * @param array<string, ObjectMapperException[]> &$errors
     *
     * @return void
     */
    private function collectUnknownKeys(array $properties, array $data, array &$errors): void
    {
        $consumed = [];

        foreach ($properties as $property) {
            $resolvedKey = $this->getResolvedDataKey($property, $data);

            if ($resolvedKey !== null) {
                $consumed[$resolvedKey] = true;
            }
        }

        if ($this->constructorClass !== null) {
            foreach ((new ConstructorMapper($this->constructorClass))->consumedKeys($data) as $key) {
                $consumed[$key] = true;
            }
        }

        foreach (array_keys($data) as $key) {
            if (!isset($consumed[$key])) {
                $errors[$key][] = new UnknownDataKeyException((string)$key);
            }
        }
    }

    /**
     * @param object                                  $target
     * @param array|Property[]                        $properties
     * @param array<string, ObjectMapperException[]> &$errors
     *
     * @return void
     */
    private function collectMissingRequired(object $target, array $properties, array &$errors): void
    {
        foreach ($properties as $property) {
            if ($property->isReadOnly() || $property->isIgnored() || $property->isNullable()) {
                continue;
            }

            // A property whose value already failed is reported once — a conversion
            // error must not produce an extra false "no value provided" entry.
            if (isset($errors[$property->getObjectPropertyName()])) {
                continue;
            }

            if (!$property->isInitialized($target)) {
                $errors[$property->getObjectPropertyName()][] = new MissingRequiredValueException($property->getObjectPropertyName());
            }
        }
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
        if ($property->isReadOnly() || $property->isIgnored()) {
            return null;
        }

        $originalName     = $property->getOriginalName();
        $snakeCaseName    = $property->getSnakeCaseName();
        $otherCaseAllowed = config('object_mapper.snake_case_to_camel');

        if (str_contains($originalName, '.')) {
            $value = Arr::get($data, $originalName);

            if ($value === null && $otherCaseAllowed) {
                $value = Arr::get($data, KeyPath::snake($originalName));
            }

            return $value;
        }

        return match (true) {
            isset($data[$originalName])                       => $data[$originalName],
            $otherCaseAllowed && isset($data[$snakeCaseName]) => $data[$snakeCaseName],
            default                                           => null,
        };
    }

    /**
     * The top-level data key this property reads from, or null when the data has no matching key.
     * Ignored and readonly properties still consume their key (they are declared, not unknown).
     *
     * @param Property $property
     * @param array    $data
     *
     * @return string|null
     */
    private function getResolvedDataKey(Property $property, array $data): ?string
    {
        $originalName = $property->getOriginalName();
        $topLevelName = str_contains($originalName, '.') ? explode('.', $originalName)[0] : $originalName;

        if (array_key_exists($topLevelName, $data)) {
            return $topLevelName;
        }

        $snakeCaseName = Str::snake($topLevelName);

        if (config('object_mapper.snake_case_to_camel') && array_key_exists($snakeCaseName, $data)) {
            return $snakeCaseName;
        }

        return null;
    }

    /**
     * @param Property $property
     * @param array    $data
     *
     * @return bool
     */
    private function hasExplicitNull(Property $property, array $data): bool
    {
        $originalName = $property->getOriginalName();

        if (str_contains($originalName, '.')) {
            if (Arr::has($data, $originalName)) {
                return Arr::get($data, $originalName) === null;
            }

            $snakeCasePath = KeyPath::snake($originalName);

            return config('object_mapper.snake_case_to_camel') === true
                && Arr::has($data, $snakeCasePath)
                && Arr::get($data, $snakeCasePath) === null;
        }

        if (array_key_exists($originalName, $data)) {
            return $data[$originalName] === null;
        }

        $snakeCaseName = $property->getSnakeCaseName();

        return config('object_mapper.snake_case_to_camel') === true
            && array_key_exists($snakeCaseName, $data)
            && $data[$snakeCaseName] === null;
    }
}
