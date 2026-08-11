<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\Property;
use Shureban\LaravelObjectMapper\Support\ClassMetadata;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\SimpleTypeClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\StaticMembersClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\WithSetterClass;

class ClassMetadataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ClassMetadata::flush();
    }

    public function test_sameInstanceReturnedForSameClass()
    {
        $this->assertSame(ClassMetadata::for(SimpleTypeClass::class), ClassMetadata::for(SimpleTypeClass::class));
        $this->assertSame(ClassMetadata::for(new SimpleTypeClass()), ClassMetadata::for(SimpleTypeClass::class));
    }

    public function test_propertiesAreMemoizedPropertyObjects()
    {
        $meta  = ClassMetadata::for(SimpleTypeClass::class);
        $first = $meta->getProperties();

        $this->assertContainsOnlyInstancesOf(Property::class, $first);
        $this->assertSame($first, $meta->getProperties());
    }

    public function test_staticPropertiesExcluded()
    {
        $names = array_map(
            fn(Property $property) => $property->getObjectPropertyName(),
            ClassMetadata::for(StaticMembersClass::class)->getProperties()
        );

        $this->assertEquals(['name'], $names);
    }

    public function test_publicSetterDetection()
    {
        $meta = ClassMetadata::for(WithSetterClass::class);

        $this->assertTrue($meta->hasPublicSetter('setFromJsonValue'));
        $this->assertFalse($meta->hasPublicSetter('setMissing'));
        $this->assertFalse(ClassMetadata::for(StaticMembersClass::class)->hasPublicSetter('setStaticSetterTarget'));
    }
}
