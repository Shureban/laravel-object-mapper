<?php

namespace Shureban\LaravelObjectMapper\Types\Custom;

use ReflectionClass;
use Shureban\LaravelObjectMapper\Exceptions\InvalidValueTypeException;
use Shureban\LaravelObjectMapper\Exceptions\WrongConstructorParametersNumberException;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;

class CustomType extends ObjectType
{
    private string $classNamespace;

    public function __construct(string $classNamespace)
    {
        $this->classNamespace = $classNamespace;
    }

    /**
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
        $requiredParameters = is_null($constructor) ? 0 : $constructor->getNumberOfRequiredParameters();

        if (is_array($value) || is_object($value)) {
            if ($requiredParameters > 0) {
                throw new WrongConstructorParametersNumberException($this->classNamespace);
            }

            return (new ObjectMapper(new $this->classNamespace()))->mapFromArray((array)$value);
        }

        $constructorTakesNoParameters = is_null($constructor) || $constructor->getNumberOfParameters() === 0;

        if ($constructorTakesNoParameters || $requiredParameters > 1) {
            throw new WrongConstructorParametersNumberException($this->classNamespace);
        }

        return new $this->classNamespace($value);
    }
}
