<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Path\To\Class as TestClass;
use ReflectionClass;
use Shureban\LaravelObjectMapper\ClassExtraInformation;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\Import2Target;

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
            'Shureban\LaravelObjectMapper\Tests\TestCase',
            (new ClassExtraInformation($class))->getFullObjectUseNamespace('TestCase')
        );
        $this->assertEquals(
            'ReflectionClass',
            (new ClassExtraInformation($class))->getFullObjectUseNamespace('ReflectionClass')
        );
        $this->assertEquals(
            'Shureban\LaravelObjectMapper\Tests\Unit\Structs\Import2Target',
            (new ClassExtraInformation($class))->getFullObjectUseNamespace('Import2Target')
        );
    }
}
