<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs\RealCases;

class Exld001FileClass
{
    public string $id;
    public string $url;
}

class Exld001ArchiveClass extends Exld001FileClass
{
    public Exld001BucketClass         $bucket;
    public Exld001ArchiveFileMapClass $file_map;
}

class Exld001BucketClass
{
    public string $id;
    public int    $size;
}

class Exld001ArchiveFileMapClass
{
    /** @var Exld001ArchiveFileClass[] */
    public array $files = [];
    /** @var Exld001ArchiveFileMapClass[] */
    public array $directories = [];
}

class Exld001ArchiveFileClass
{
    public string $name;
    public float  $size;
    public string $mime_type;
    public string $extension;
}
