<?php

namespace Shureban\LaravelObjectMapper\Contracts;

/**
 * Marker interface: classes implementing it are automatically resolved from the
 * current request when type-hinted in controllers (see ObjectMapperServiceProvider).
 */
interface MapsFromRequest
{
}
