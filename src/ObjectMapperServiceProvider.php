<?php

namespace Shureban\LaravelObjectMapper;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\ServiceProvider;
use Shureban\LaravelObjectMapper\Contracts\MapsFromRequest;

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

        $this->app->beforeResolving(MapsFromRequest::class, function (string $abstract, array $parameters, Container $app) {
            if ($app->has($abstract)) {
                return;
            }

            $app->bind($abstract, function (Container $container) use ($abstract) {
                $request = $container->get('request');

                return $request instanceof FormRequest
                    ? (new ObjectMapper($abstract))->mapFromRequest($request)
                    : (new ObjectMapper($abstract))->mapFromArray($request->all());
            });
        });
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
