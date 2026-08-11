<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\EnumFallbackClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\TestEnum;

class EnumFallbackTest extends TestCase
{
    public function test_unknownValueResolvesToFallbackCase()
    {
        $result = (new ObjectMapper(new EnumFallbackClass()))->mapFromArray(['suit' => 'garbage']);

        $this->assertSame(TestEnum::Hearts, $result->suit);
    }

    public function test_validValueStillMapsNormally()
    {
        $result = (new ObjectMapper(new EnumFallbackClass()))->mapFromArray(['suit' => 'Spades']);

        $this->assertSame(TestEnum::Spades, $result->suit);
    }
}
