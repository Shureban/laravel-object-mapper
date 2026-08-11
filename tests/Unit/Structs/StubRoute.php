<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit\Structs;

class StubRoute
{
    /**
     * @param string|null $key
     *
     * @return mixed
     */
    public function getAction(?string $key = null): mixed
    {
        return StubController::class . '@store';
    }
}
