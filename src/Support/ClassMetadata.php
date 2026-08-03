<?php

namespace Shureban\LaravelObjectMapper\Support;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Shureban\LaravelObjectMapper\Property;

class ClassMetadata
{
    /** @var array<class-string, self> */
    private static array $cache = [];

    /** @var array|Property[]|null */
    private ?array $properties = null;

    /** @var array<string, bool>|null */
    private ?array $publicSetters = null;

    private ReflectionClass $reflection;

    /**
     * @param ReflectionClass $reflection
     */
    private function __construct(ReflectionClass $reflection)
    {
        $this->reflection = $reflection;
    }

    /**
     * @param class-string|object $class
     *
     * @return self
     */
    public static function for(string|object $class): self
    {
        $className = is_object($class) ? get_class($class) : $class;

        return self::$cache[$className] ??= new self(new ReflectionClass($className));
    }

    /**
     * @return void
     */
    public static function flush(): void
    {
        self::$cache = [];
    }

    /**
     * @return array|Property[]
     */
    public function getProperties(): array
    {
        if ($this->properties !== null) {
            return $this->properties;
        }

        $reflectProps = $this->reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $reflectProps = array_filter($reflectProps, fn(ReflectionProperty $property) => !$property->isStatic());

        return $this->properties = array_map(fn(ReflectionProperty $property) => new Property($property), array_values($reflectProps));
    }

    /**
     * @param string $methodName
     *
     * @return bool
     */
    public function hasPublicSetter(string $methodName): bool
    {
        if ($this->publicSetters === null) {
            $methods = $this->reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            $methods = array_filter($methods, fn(ReflectionMethod $method) => !$method->isStatic());

            $this->publicSetters = array_fill_keys(array_map(fn(ReflectionMethod $method) => $method->getName(), $methods), true);
        }

        return isset($this->publicSetters[$methodName]);
    }

    /**
     * @return ReflectionClass
     */
    public function getReflection(): ReflectionClass
    {
        return $this->reflection;
    }
}
