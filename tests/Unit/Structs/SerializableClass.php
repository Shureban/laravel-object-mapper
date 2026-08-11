<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Carbon\Carbon;
use Shureban\LaravelObjectMapper\Attributes\DateFormat;
use Shureban\LaravelObjectMapper\Attributes\Ignore;
use Shureban\LaravelObjectMapper\Attributes\MapFrom;
use Shureban\LaravelObjectMapper\MappableTrait;

class SerializableClass
{
    use MappableTrait;

    #[MapFrom('user_id')]
    public int $id;

    public TestEnum $suit;

    #[DateFormat('Y-m-d')]
    public Carbon $birthday;

    public CustomTypeForArrayOf $nested;

    #[Ignore]
    public string $secret = 'top-secret';

    public string $uninitialized;

    public string $camelCaseName = 'value';
}
