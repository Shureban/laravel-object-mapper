<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Path\To\Class as TestClass;
use ReflectionClass;
use Shureban\LaravelObjectMapper\ClassExtraInformation;
use Tests\TestCase;

class TestObject
{
    public TestClass $class;
}

class ClassExtraInformationTest extends TestCase
{
    public function test_getNamespace()
    {
        $class = new ReflectionClass($this);

        $this->assertEquals('Shureban\LaravelObjectMapper\Tests\Unit', (new ClassExtraInformation($class))->getNamespace());
    }

    public function test_getFullObjectUseNamespace()
    {
        $class = new ReflectionClass($this);

        $this->assertEquals(
            'Shureban\LaravelObjectMapper\Tests\Unit\TestObject',
            (new ClassExtraInformation($class))->getFullObjectUseNamespace('TestObject')
        );
        $this->assertEquals(
            'Path\To\Class',
            (new ClassExtraInformation($class))->getFullObjectUseNamespace('TestClass')
        );
        $this->assertEquals(
            'Tests\TestCase',
            (new ClassExtraInformation($class))->getFullObjectUseNamespace('TestCase')
        );
        $this->assertNull(
            (new ClassExtraInformation($class))->getFullObjectUseNamespace('ReflectionClass')
        );
    }
}
