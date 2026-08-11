<?php

namespace Shureban\LaravelObjectMapper\Support;

use Illuminate\Support\Str;

class KeyPath
{
    /**
     * Snake-cases every segment of a dotted data path: shippingAddress.city -> shipping_address.city.
     *
     * @param string $path
     *
     * @return string
     */
    public static function snake(string $path): string
    {
        return implode('.', array_map(fn(string $segment) => Str::snake($segment), explode('.', $path)));
    }
}
