<?php

namespace Shureban\LaravelObjectMapper\Tests\Structs;

use Carbon\Carbon;
use DateTime;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Tests\TestCase;

class SimpleBoxClass
{
    public DateTime $dateTime;
    public Carbon   $carbon;
}

class BoxTypeTest extends TestCase
{
    public function test_DateTime()
    {
        $this->assertEquals(new DateTime('2022-01-01'), (new ObjectMapper(new SimpleBoxClass()))->mapFromJson('{"dateTime": "2022-01-01"}')->dateTime);
        $this->assertEquals(new DateTime('2022-01-01 10:20:30'), (new ObjectMapper(new SimpleBoxClass()))->mapFromJson('{"dateTime": "2022-01-01 10:20:30"}')->dateTime);
        $this->assertEquals(new DateTime('2022-01-01'), (new ObjectMapper(new SimpleBoxClass()))->mapFromArray(['dateTime' => '2022-01-01'])->dateTime);
        $this->assertEquals(new DateTime('2022-01-01 10:20:30'), (new ObjectMapper(new SimpleBoxClass()))->mapFromArray(['dateTime' => '2022-01-01 10:20:30'])->dateTime);
    }

    public function test_Carbon()
    {
        $this->assertEquals(new Carbon('2022-01-01'), (new ObjectMapper(new SimpleBoxClass()))->mapFromJson('{"carbon": "2022-01-01"}')->carbon);
        $this->assertEquals(new Carbon('2022-01-01 10:20:30'), (new ObjectMapper(new SimpleBoxClass()))->mapFromJson('{"carbon": "2022-01-01 10:20:30"}')->carbon);
        $this->assertEquals(new Carbon('2022-01-01'), (new ObjectMapper(new SimpleBoxClass()))->mapFromArray(['carbon' => '2022-01-01'])->carbon);
        $this->assertEquals(new Carbon('2022-01-01 10:20:30'), (new ObjectMapper(new SimpleBoxClass()))->mapFromArray(['carbon' => '2022-01-01 10:20:30'])->carbon);
    }
}
