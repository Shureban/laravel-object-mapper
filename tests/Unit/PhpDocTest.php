<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\PhpDoc;
use Tests\TestCase;

class PhpDocTest extends TestCase
{
    public function test_getPropertyName()
    {
        $this->assertEquals('variable', (new PhpDoc('/** @var $variable */'))->getPropertyName());
        $this->assertEquals('variable1', (new PhpDoc('/** @var $variable1 */'))->getPropertyName());
        $this->assertEquals('variable_variable', (new PhpDoc('/** @var $variable_variable */'))->getPropertyName());
        $this->assertEquals('variable', (new PhpDoc('/** @var string $variable */'))->getPropertyName());
        $this->assertEquals('variable', (new PhpDoc(<<<DOC
/**
* @var string \$variable
*/
DOC
        ))->getPropertyName());
    }

    public function test_getPropertyType()
    {
        $this->assertEquals('string', (new PhpDoc('/** @var string[] */'))->getPropertyType());
        $this->assertEquals('string', (new PhpDoc('/** @var string[][] */'))->getPropertyType());
        $this->assertEquals('string', (new PhpDoc('/** @var string[][][] */'))->getPropertyType());
        $this->assertEquals('string', (new PhpDoc('/** @var string */'))->getPropertyType());
        $this->assertEquals('string', (new PhpDoc('/** @var string $variable */'))->getPropertyType());
        $this->assertEquals('SomeClass', (new PhpDoc('/** @var SomeClass $variable */'))->getPropertyType());
        $this->assertEquals('Path\To\Some\Class', (new PhpDoc('/** @var Path\To\Some\Class $variable */'))->getPropertyType());
        $this->assertEquals('\Path\To\Some\Class', (new PhpDoc('/** @var \Path\To\Some\Class $variable */'))->getPropertyType());
        $this->assertEquals('string', (new PhpDoc(<<<DOC
/**
* @var string \$variable
*/
DOC
        ))->getPropertyType());


        //        $this->assertEquals('string', (new PhpDoc('/** @var string[] */'))->getPropertyType());
    }

    public function test_isArrayOf()
    {
        $this->assertFalse((new PhpDoc('/** @var string */'))->isArrayOf());
        $this->assertTrue((new PhpDoc('/** @var string[] */'))->isArrayOf());
        $this->assertTrue((new PhpDoc('/** @var string[][] */'))->isArrayOf());
        $this->assertTrue((new PhpDoc('/** @var string[][][] */'))->isArrayOf());
        $this->assertTrue((new PhpDoc('/** @var string[] $variable */'))->isArrayOf());
        $this->assertTrue((new PhpDoc(<<<DOC
/**
* @var string[][][] \$variable
*/
DOC
        ))->isArrayOf());
    }

    public function test_arrayNestedLevel()
    {
        $this->assertEquals(0, (new PhpDoc('/** @var string */'))->arrayNestedLevel());
        $this->assertEquals(1, (new PhpDoc('/** @var string[] */'))->arrayNestedLevel());
        $this->assertEquals(2, (new PhpDoc('/** @var string[][] */'))->arrayNestedLevel());
        $this->assertEquals(3, (new PhpDoc('/** @var string[][][] */'))->arrayNestedLevel());
        $this->assertEquals(1, (new PhpDoc('/** @var string[] $variable */'))->arrayNestedLevel());
        $this->assertEquals(3, (new PhpDoc(<<<DOC
/**
* @var string[][][] \$variable
*/
DOC
        ))->arrayNestedLevel());
    }
}
