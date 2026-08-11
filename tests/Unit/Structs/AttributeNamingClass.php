<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Attributes\Ignore;
use Shureban\LaravelObjectMapper\Attributes\MapFrom;

class AttributeNamingClass
{
    #[MapFrom('user_id')]
    public int $id;

    /** @var string $doc_name */
    #[MapFrom('attr_name')]
    public string $attributeWinsOverPhpDoc;

    #[MapFrom('data.attributes.title')]
    public string $nested;

    #[Ignore]
    public string $secret = 'untouched';
}
