<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\AttributeNamingClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\DottedMapFromClass;

class AttributesMapFromTest extends TestCase
{
    public function test_mapFromOverridesKey()
    {
        $result = (new ObjectMapper(new AttributeNamingClass()))->mapFromArray(['user_id' => 7]);

        $this->assertSame(7, $result->id);
    }

    public function test_attributeWinsOverPhpDocName()
    {
        $result = (new ObjectMapper(new AttributeNamingClass()))->mapFromArray(['attr_name' => 'a', 'doc_name' => 'd']);

        $this->assertSame('a', $result->attributeWinsOverPhpDoc);
    }

    public function test_dotNotationReadsNestedValue()
    {
        $result = (new ObjectMapper(new AttributeNamingClass()))->mapFromArray([
            'data' => ['attributes' => ['title' => 'Hello']],
        ]);

        $this->assertSame('Hello', $result->nested);
    }

    public function test_ignoredPropertyIsNeverMapped()
    {
        $result = (new ObjectMapper(new AttributeNamingClass()))->mapFromArray(['secret' => 'hacked']);

        $this->assertSame('untouched', $result->secret);
    }

    public function test_dottedKeyFallsBackToSnakeCaseSegments()
    {
        $result = (new ObjectMapper(new DottedMapFromClass()))->mapFromArray([
            'shipping_address' => ['city' => 'Kyiv'],
        ]);

        $this->assertSame('Kyiv', $result->city);
    }
}
