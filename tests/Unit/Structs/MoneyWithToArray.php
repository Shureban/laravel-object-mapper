<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class MoneyWithToArray
{
    private int $amount = 0;

    public static function fromAmount(int $amount): self
    {
        $instance         = new self();
        $instance->amount = $amount;

        return $instance;
    }

    public function toArray(): array
    {
        return ['amount' => $this->amount];
    }
}
