<?php

namespace Shureban\LaravelObjectMapper;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\ServiceProvider;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
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

                if ($request instanceof FormRequest) {
                    return (new ObjectMapper($abstract))->mapFromRequest($request);
                }

                // Laravel never binds a FormRequest as 'request': the validated data
                // lives in the FormRequest declared by the route action, so resolve
                // that one through the container (which triggers its validation).
                $formRequestClass = $this->resolveRouteFormRequestClass($request);

                if ($formRequestClass !== null) {
                    /** @var FormRequest $formRequest */
                    $formRequest = $container->make($formRequestClass);

                    return (new ObjectMapper($abstract))->mapFromRequest($formRequest);
                }

                return (new ObjectMapper($abstract))->mapFromArray($request->all());
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

    /**
     * The FormRequest subclass declared in the current route action's signature,
     * or null when the request has no route / the action declares none.
     *
     * @param object $request
     *
     * @return class-string<FormRequest>|null
     */
    private function resolveRouteFormRequestClass(object $request): ?string
    {
        if (!method_exists($request, 'route')) {
            return null;
        }

        $route = $request->route();

        if (!is_object($route) || !method_exists($route, 'getAction')) {
            return null;
        }

        $action = $this->reflectRouteAction($route->getAction('uses'));

        if ($action === null) {
            return null;
        }

        foreach ($action->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && is_subclass_of($type->getName(), FormRequest::class)) {
                return $type->getName();
            }
        }

        return null;
    }

    /**
     * @param mixed $uses Controller@method string or route Closure.
     *
     * @return ReflectionFunctionAbstract|null
     */
    private function reflectRouteAction(mixed $uses): ?ReflectionFunctionAbstract
    {
        if ($uses instanceof Closure) {
            return new ReflectionFunction($uses);
        }

        if (!is_string($uses) || !str_contains($uses, '@')) {
            return null;
        }

        [$class, $method] = explode('@', $uses, 2);

        return method_exists($class, $method) ? new ReflectionMethod($class, $method) : null;
    }
}
