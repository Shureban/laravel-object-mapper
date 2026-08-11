<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Shureban\LaravelObjectMapper\ObjectMapperServiceProvider;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\RequestDtoClass;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\StubRoute;

class RequestInjectionTest extends TestCase
{
    public function test_dtoIsResolvedFromCurrentRequest()
    {
        $app = $this->makeApp(Request::create('/', 'POST', ['id' => 7, 'name' => 'John']));

        $dto = $app->make(RequestDtoClass::class);

        $this->assertInstanceOf(RequestDtoClass::class, $dto);
        $this->assertSame(7, $dto->id);
        $this->assertSame('John', $dto->name);

        Container::setInstance(null);
    }

    public function test_dtoIsBuiltFromRouteFormRequestValidatedData()
    {
        $request = Request::create('/', 'POST', ['id' => 99, 'name' => 'Hacker', 'role' => 'admin']);
        $request->setRouteResolver(fn() => new StubRoute());

        $app = $this->makeApp($request);

        $dto = $app->make(RequestDtoClass::class);

        $this->assertSame(7, $dto->id);
        $this->assertSame('John', $dto->name);

        Container::setInstance(null);
    }

    /**
     * @param Request $request
     *
     * @return Application
     */
    private function makeApp(Request $request): Application
    {
        $app = new Application();
        $app->instance('config', new Repository([
            'object_mapper' => require __DIR__ . '/../../config/object_mapper.php',
            'app'           => ['timezone' => 'UTC'],
        ]));
        $app->instance('request', $request);

        (new ObjectMapperServiceProvider($app))->register();

        return $app;
    }
}
