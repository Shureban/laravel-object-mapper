<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ModelTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SomeModel;
use Tests\TestCase;

class ModelTypeTest extends TestCase
{
    public function test_Model()
    {
        $model              = new ModelTypeClass();
        $model->phpDocModel = new SomeModel(10);
        $model->setterModel = new SomeModel(20);

        $this->assertEquals($model->phpDocModel, (new ObjectMapper(new ModelTypeClass()))->mapFromJson('{"model_id": 10}')->phpDocModel);
        $this->assertEquals($model->setterModel, (new ObjectMapper(new ModelTypeClass()))->mapFromJson('{"setterModel": 20}')->setterModel);
    }
}
