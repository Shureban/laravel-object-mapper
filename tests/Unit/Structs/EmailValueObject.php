<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class EmailValueObject
{
    public string $value = '';

    private function __construct()
    {
    }

    public static function from(string $email): self
    {
        $instance        = new self();
        $instance->value = strtolower($email);

        return $instance;
    }
}
