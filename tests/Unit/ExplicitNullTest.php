<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\NullableCastClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\NullableDefaultsClass;

class ExplicitNullTest extends TestCase
{
    public function test_defaultConfigSkipsExplicitNull()
    {
        $result = (new ObjectMapper(new NullableDefaultsClass()))->mapFromArray(['x' => null]);

        $this->assertSame(5, $result->x);
    }

    public function test_enabledConfigAssignsNullToNullableProperty()
    {
        config()->set('object_mapper.assign_explicit_null', true);

        $result = (new ObjectMapper(new NullableDefaultsClass()))->mapFromArray(['x' => null]);

        $this->assertNull($result->x);
    }

    public function test_enabledConfigKeepsNonNullablePropertyUntouched()
    {
        config()->set('object_mapper.assign_explicit_null', true);

        $result = (new ObjectMapper(new NullableDefaultsClass()))->mapFromArray(['y' => null]);

        $this->assertSame(1, $result->y);
    }

    public function test_converterReturningNullIsAssignedToNullableProperty()
    {
        $result = (new ObjectMapper(new NullableCastClass()))->mapFromArray(['nickname' => 'N/A', 'other' => 'N/A']);

        $this->assertNull($result->nickname);
        $this->assertNull($result->other);
    }

    public function test_converterReturningValueStillAssigns()
    {
        $result = (new ObjectMapper(new NullableCastClass()))->mapFromArray(['nickname' => 'john']);

        $this->assertSame('JOHN', $result->nickname);
    }
}
