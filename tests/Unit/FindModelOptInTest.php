<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\Exceptions\ImplicitModelLookupException;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Support\ClassMetadata;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\FindModelClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ModelTypeClass;

class FindModelOptInTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ClassMetadata::flush();
    }

    public function test_modelPropertyWithoutOptInThrows()
    {
        config()->set('object_mapper.implicit_model_lookup', false);

        $this->assertThrows(
            fn() => (new ObjectMapper(new ModelTypeClass()))->mapFromArray(['model_id' => 10]),
            ImplicitModelLookupException::class
        );
    }

    public function test_findModelAttributeAllowsLookup()
    {
        config()->set('object_mapper.implicit_model_lookup', false);

        $result = (new ObjectMapper(new FindModelClass()))->mapFromArray(['model' => 10]);

        $this->assertSame(10, $result->model->id);
    }

    public function test_configFlagRestoresImplicitLookup()
    {
        config()->set('object_mapper.implicit_model_lookup', true);

        $result = (new ObjectMapper(new ModelTypeClass()))->mapFromJson('{"model_id": 10}');

        $this->assertSame(10, $result->phpDocModel->id);
    }
}
