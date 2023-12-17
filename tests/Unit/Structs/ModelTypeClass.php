<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

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
        $this->setterModel = SomeModel::find($value);
    }
}
