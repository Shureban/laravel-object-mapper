<?php

namespace Shureban\LaravelObjectMapper\Types\Custom;

use Illuminate\Database\Eloquent\Model;
use Shureban\LaravelObjectMapper\Types\SimpleTypes\ObjectType;

class ModelType extends ObjectType
{
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param mixed $value
     *
     * @return Model
     */
    public function convert(mixed $value): Model
    {
        return $this->model->find($value);
    }
}
