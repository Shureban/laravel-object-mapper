<?php

namespace Shureban\LaravelObjectMapper\Types\Custom;

use Illuminate\Database\Eloquent\Model;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;

class ModelType extends ObjectType
{
    private string $modelNamespace;

    public function __construct(string $modelNamespace)
    {
        $this->modelNamespace = $modelNamespace;
    }

    /**
     * @param mixed $value
     *
     * @return Model
     */
    public function convert(mixed $value): Model
    {
        return call_user_func([$this->modelNamespace, 'find'], $value);
    }
}
