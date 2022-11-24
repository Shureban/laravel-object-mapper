<?php

namespace Shureban\LaravelObjectMapper\Tests\Structs;

use Carbon\Carbon;
use DateTime;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\BoxTypeClass;
use Tests\TestCase;

class BoxTypeTest extends TestCase
{
    public function test_DateTime()
    {
        $this->assertEquals(new DateTime('2022-01-01'), (new ObjectMapper(new BoxTypeClass()))->mapFromJson('{"dateTime": "2022-01-01"}')->dateTime);
        $this->assertEquals(new DateTime('2022-01-01 10:20:30'), (new ObjectMapper(new BoxTypeClass()))->mapFromJson('{"dateTime": "2022-01-01 10:20:30"}')->dateTime);
        $this->assertEquals(new DateTime('2022-01-01'), (new ObjectMapper(new BoxTypeClass()))->mapFromArray(['dateTime' => '2022-01-01'])->dateTime);
        $this->assertEquals(new DateTime('2022-01-01 10:20:30'), (new ObjectMapper(new BoxTypeClass()))->mapFromArray(['dateTime' => '2022-01-01 10:20:30'])->dateTime);
    }

    public function test_Carbon()
    {
        $this->assertEquals(new Carbon('2022-01-01'), (new ObjectMapper(new BoxTypeClass()))->mapFromJson('{"carbon": "2022-01-01"}')->carbon);
        $this->assertEquals(new Carbon('2022-01-01 10:20:30'), (new ObjectMapper(new BoxTypeClass()))->mapFromJson('{"carbon": "2022-01-01 10:20:30"}')->carbon);
        $this->assertEquals(new Carbon('2022-01-01'), (new ObjectMapper(new BoxTypeClass()))->mapFromArray(['carbon' => '2022-01-01'])->carbon);
        $this->assertEquals(new Carbon('2022-01-01 10:20:30'), (new ObjectMapper(new BoxTypeClass()))->mapFromArray(['carbon' => '2022-01-01 10:20:30'])->carbon);
    }

    public function test_Collection()
    {
        $this->assertEquals(collect([1, 2, 3]), (new ObjectMapper(new BoxTypeClass()))->mapFromJson('{"collection": [1,2,3]}')->collection);
        $this->assertEquals(collect([1, 2, 3]), (new ObjectMapper(new BoxTypeClass()))->mapFromArray(['collection' => [1, 2, 3]])->collection);
    }
}
