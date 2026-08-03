<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\ValueObjectHolderClass;

class ValueObjectFactoryTest extends TestCase
{
    public function test_scalarValueGoesThroughStaticFromFactory()
    {
        $result = (new ObjectMapper(new ValueObjectHolderClass()))->mapFromArray(['email' => 'USER@Example.COM']);

        $this->assertSame('user@example.com', $result->email->value);
    }

    public function test_classesWithoutFromFactoryKeepConstructorBehavior()
    {
        $result = (new ObjectMapper(new ValueObjectHolderClass()))->mapFromArray(['viaConstructor' => 42]);

        $this->assertSame(42, $result->viaConstructor->id);
    }
}
