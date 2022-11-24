<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Carbon\Carbon;
use DateTime;
use Shureban\LaravelObjectMapper\ObjectMapper;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\PhpDocBoxTypeClass;
use Tests\TestCase;

class PhpDocBoxTypeTest extends TestCase
{
    public function test_DateTime()
    {
        $this->assertEquals(new DateTime('2022-01-01'), (new ObjectMapper(new PhpDocBoxTypeClass()))->mapFromJson('{"dateTime": "2022-01-01"}')->dateTime);
        $this->assertEquals(new DateTime('2022-01-01 10:20:30'), (new ObjectMapper(new PhpDocBoxTypeClass()))->mapFromJson('{"dateTime": "2022-01-01 10:20:30"}')->dateTime);
        $this->assertEquals(new DateTime('2022-01-01'), (new ObjectMapper(new PhpDocBoxTypeClass()))->mapFromArray(['dateTime' => '2022-01-01'])->dateTime);
        $this->assertEquals(new DateTime('2022-01-01 10:20:30'), (new ObjectMapper(new PhpDocBoxTypeClass()))->mapFromArray(['dateTime' => '2022-01-01 10:20:30'])->dateTime);
    }

    public function test_Carbon()
    {
        $this->assertEquals(new Carbon('2022-01-01'), (new ObjectMapper(new PhpDocBoxTypeClass()))->mapFromJson('{"carbon": "2022-01-01"}')->carbon);
        $this->assertEquals(new Carbon('2022-01-01 10:20:30'), (new ObjectMapper(new PhpDocBoxTypeClass()))->mapFromJson('{"carbon": "2022-01-01 10:20:30"}')->carbon);
        $this->assertEquals(new Carbon('2022-01-01'), (new ObjectMapper(new PhpDocBoxTypeClass()))->mapFromArray(['carbon' => '2022-01-01'])->carbon);
        $this->assertEquals(new Carbon('2022-01-01 10:20:30'), (new ObjectMapper(new PhpDocBoxTypeClass()))->mapFromArray(['carbon' => '2022-01-01 10:20:30'])->carbon);
    }

    public function test_Collection()
    {
        $this->assertEquals(collect([1, 2, 3]), (new ObjectMapper(new PhpDocBoxTypeClass()))->mapFromJson('{"collection": [1,2,3]}')->collection);
        $this->assertEquals(collect([1, 2, 3]), (new ObjectMapper(new PhpDocBoxTypeClass()))->mapFromArray(['collection' => [1, 2, 3]])->collection);
    }
}
