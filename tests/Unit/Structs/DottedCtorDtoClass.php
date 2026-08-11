<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Attributes\MapFrom;
use Shureban\LaravelObjectMapper\MappableTrait;

class DottedCtorDtoClass
{
    use MappableTrait;

    public function __construct(
        #[MapFrom('userProfile.firstName')]
        public readonly string $firstName,
    ) {
    }
}
