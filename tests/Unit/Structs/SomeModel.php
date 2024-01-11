<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

use Illuminate\Database\Eloquent\Model;

class SomeModel extends Model
{
    public ?int $id;

    public function __construct(int $id = null)
    {
        parent::__construct([]);

        $this->id = $id;
    }

    public static function find($id, $columns = []): ?SomeModel
    {
        return new SomeModel($id);
    }
}
