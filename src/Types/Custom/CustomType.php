<?php

namespace Shureban\LaravelObjectMapper\Types\Custom;

use ReflectionClass;
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
     */
    public function convert(mixed $value): object
    {
        $reflection       = new ReflectionClass($this->classNamespace);
        $constructor      = $reflection->getConstructor();
        $emptyConstructor = !is_null($constructor) && $constructor->getNumberOfParameters() === 0;

        if (is_null($constructor) || $emptyConstructor || is_array($value) || is_object($value)) {
            return (new ObjectMapper(new $this->classNamespace()))->mapFromArray($value);
        }

        $tooManyRequiredParameters = $constructor->getNumberOfRequiredParameters() > 1;

        if ($tooManyRequiredParameters) {
            throw new WrongConstructorParametersNumberException($this->classNamespace);
        }

        return new $this->classNamespace($value);
    }
}
