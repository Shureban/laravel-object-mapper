<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Illuminate\Database\Eloquent\Model;

class SomeModel extends Model
{
    public ?int $id;

    public function __construct(int $id = null)
    {
        parent::__construct([]);

        $this->id = $id;
    }

    public function find($id, $columns = []): ?SomeModel
    {
        return new SomeModel($id);
    }
}

class ModelTypeClass
{
    /** @var SomeModel $model_id */
    public SomeModel $phpDocModel;
    public SomeModel $setterModel;

    /**
     * @param mixed $value
     */
    public function setSetterModel(mixed $value): void
    {
        $this->setterModel = (new SomeModel())->find($value);
    }
}
