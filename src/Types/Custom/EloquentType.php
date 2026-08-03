<?php

namespace Shureban\LaravelObjectMapper\Types\Custom;

use Illuminate\Database\Eloquent\Model;
use Shureban\LaravelObjectMapper\Exceptions\InvalidModelKeyException;
use Shureban\LaravelObjectMapper\Types\Type;

class EloquentType extends Type
{
    private string $modelNamespace;

    public function __construct(string $modelNamespace)
    {
        $this->modelNamespace = $modelNamespace;
    }

    /**
     * @param mixed $value
     *
     * @return Model|null Null when the model is not found (the property keeps its previous value).
     * @throws InvalidModelKeyException
     */
    public function convert(mixed $value): ?Model
    {
        if ($value instanceof $this->modelNamespace) {
            return $value;
        }

        if (!is_int($value) && !is_string($value)) {
            throw new InvalidModelKeyException($this->modelNamespace, $value);
        }

        return call_user_func([$this->modelNamespace, 'find'], $value);
    }

    /**
     * @return mixed
     */
    public function getDefaultValue(): mixed
    {
        return null;
    }
}
