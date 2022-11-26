<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\Attributes\SetterName;
use Tests\TestCase;

class SetterNameTest extends TestCase
{
    public function test_SetterName()
    {
        $this->assertEquals('setSimple', (string)new SetterName('simple'));
        $this->assertEquals('setCamelCaseMethod', (string)new SetterName('camelCaseMethod'));
        $this->assertEquals('setSnakeCaseMethod', (string)new SetterName('snake_case_method'));
    }
}
