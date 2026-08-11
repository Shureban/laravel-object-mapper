<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Illuminate\Database\Eloquent\Model;

class NullFindModel extends Model
{
    public static function find($id, $columns = []): ?NullFindModel
    {
        return null;
    }
}
