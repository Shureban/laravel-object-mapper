<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Shureban\LaravelObjectMapper\Attributes\FindModel;

class FindModelClass
{
    #[FindModel]
    public SomeModel $model;
}
