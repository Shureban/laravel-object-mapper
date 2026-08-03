<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class MoneyWithFromAndConstructor
{
    public int|string $raw = 0;

    public function __construct(int|string $raw)
    {
        $this->raw = $raw;
    }

    public static function from(array $data): self
    {
        return new self($data['raw']);
    }
}
