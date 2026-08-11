<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Attributes\Ignore;
use Shureban\LaravelObjectMapper\MappableTrait;

class IgnoredPromotedDtoClass
{
    use MappableTrait;

    public function __construct(
        public readonly string $email,
        #[Ignore]
        public readonly bool $isAdmin = false,
    ) {
    }
}
