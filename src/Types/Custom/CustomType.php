<?php

namespace Shureban\LaravelObjectMapper\Types\Custom;

use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;
use Shureban\LaravelObjectMapper\Exceptions\InvalidValueTypeException;
use Shureban\LaravelObjectMapper\Exceptions\WrongConstructorParametersNumberException;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;
use TypeError;

class CustomType extends ObjectType
{
    private string $classNamespace;

    public function __construct(string $classNamespace)
    {
        $this->classNamespace = $classNamespace;
    }

    /**
     * Conversion priority keeps 1.x behavior first: recursive mapping for array/object
     * payloads (constructor mapping when the constructor requires arguments), the
     * single-argument constructor for scalars, and the static from() factory as the
     * fallback for classes whose constructor is not usable (value objects).
     *
     * @param mixed $value
     *
     * @return object
     * @throws WrongConstructorParametersNumberException
     * @throws InvalidValueTypeException
     */
    public function convert(mixed $value): object
    {
        if ($value instanceof $this->classNamespace) {
            return $value;
        }

        $reflection = new ReflectionClass($this->classNamespace);

        if ($reflection->isAbstract()) {
            throw new InvalidValueTypeException($this->classNamespace, $value);
        }

        $constructor        = $reflection->getConstructor();
        $publicConstructor  = $constructor === null || $constructor->isPublic();
        $requiredParameters = is_null($constructor) ? 0 : $constructor->getNumberOfRequiredParameters();

        if (is_array($value) || is_object($value)) {
            if ($publicConstructor && $requiredParameters === 0) {
                return (new ObjectMapper(new $this->classNamespace()))->mapFromArray((array)$value);
            }

            if ($publicConstructor) {
                return (new ObjectMapper($this->classNamespace))->mapFromArray((array)$value);
            }

            return $this->convertViaFromFactory($reflection, $value);
        }

        $constructorTakesOneArgument = $constructor !== null
            && $constructor->getNumberOfParameters() >= 1
            && $requiredParameters <= 1;

        if ($publicConstructor && $constructorTakesOneArgument) {
            try {
                return new $this->classNamespace($value);
            } catch (TypeError $exception) {
                throw new InvalidValueTypeException($this->classNamespace, $value, 0, $exception);
            }
        }

        return $this->convertViaFromFactory($reflection, $value);
    }

    /**
     * @param ReflectionClass $reflection
     * @param mixed           $value
     *
     * @return object
     * @throws WrongConstructorParametersNumberException
     * @throws InvalidValueTypeException
     */
    private function convertViaFromFactory(ReflectionClass $reflection, mixed $value): object
    {
        $factory = $this->getStaticFromFactory($reflection);

        if ($factory === null) {
            throw new WrongConstructorParametersNumberException($this->classNamespace);
        }

        if (!$this->parameterAcceptsValue($factory, $value)) {
            throw new InvalidValueTypeException($this->classNamespace, $value);
        }

        try {
            $result = call_user_func([$this->classNamespace, 'from'], $value);
        } catch (TypeError $exception) {
            throw new InvalidValueTypeException($this->classNamespace, $value, 0, $exception);
        }

        if (!$result instanceof $this->classNamespace) {
            throw new InvalidValueTypeException($this->classNamespace, $value);
        }

        return $result;
    }

    /**
     * The public static from() factory, when its signature fits a single mapped value.
     *
     * @param ReflectionClass $reflection
     *
     * @return ReflectionMethod|null
     */
    private function getStaticFromFactory(ReflectionClass $reflection): ?ReflectionMethod
    {
        if (!$reflection->hasMethod('from')) {
            return null;
        }

        $method = $reflection->getMethod('from');

        $signatureFits = $method->isPublic()
            && $method->isStatic()
            && $method->getNumberOfParameters() >= 1
            && $method->getNumberOfRequiredParameters() <= 1;

        return $signatureFits ? $method : null;
    }

    /**
     * Whether the factory's first parameter can take the mapped value —
     * prevents a raw TypeError for obviously incompatible payloads.
     *
     * @param ReflectionMethod $method
     * @param mixed            $value
     *
     * @return bool
     */
    private function parameterAcceptsValue(ReflectionMethod $method, mixed $value): bool
    {
        $type = $method->getParameters()[0]->getType();

        if ($type === null) {
            return true;
        }

        $types = $type instanceof ReflectionUnionType ? $type->getTypes() : [$type];

        foreach ($types as $namedType) {
            if (!$namedType instanceof ReflectionNamedType) {
                continue;
            }

            if ($this->valueMatchesTypeName($namedType->getName(), $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $typeName
     * @param mixed  $value
     *
     * @return bool
     */
    private function valueMatchesTypeName(string $typeName, mixed $value): bool
    {
        return match ($typeName) {
            'mixed'    => true,
            'string'   => is_string($value) || is_numeric($value),
            'int'      => is_int($value),
            'float'    => is_float($value) || is_int($value),
            'bool'     => is_bool($value),
            'array'    => is_array($value),
            'iterable' => is_iterable($value),
            'object'   => is_object($value),
            default    => $value instanceof $typeName,
        };
    }
}
