<?php

namespace Shureban\LaravelObjectMapper;

use Illuminate\Support\ServiceProvider;

class ObjectMapperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/object_mapper.php', 'object_mapper');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/object_mapper.php' => config_path('object_mapper.php'),
        ], 'object-mapper-config');
    }
}
