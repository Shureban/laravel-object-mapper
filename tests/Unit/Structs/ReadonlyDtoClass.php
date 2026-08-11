<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Attributes\MapFrom;
use Shureban\LaravelObjectMapper\MappableTrait;

class ReadonlyDtoClass
{
    use MappableTrait;

    public function __construct(
        public readonly int $id,
        #[MapFrom('full_name')]
        public readonly string $name,
        public readonly string $role = 'user',
    ) {
    }
}
