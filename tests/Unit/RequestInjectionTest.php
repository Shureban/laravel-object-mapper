<?php

namespace Shureban\LaravelObjectMapper\Tests\Unit;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Shureban\LaravelObjectMapper\ObjectMapperServiceProvider;
use Shureban\LaravelObjectMapper\Tests\TestCase;
use Shureban\LaravelObjectMapper\Tests\Unit\Structs\RequestDtoClass;

class RequestInjectionTest extends TestCase
{
    public function test_dtoIsResolvedFromCurrentRequest()
    {
        $app = new Application();
        $app->instance('config', new Repository([
            'object_mapper' => require __DIR__ . '/../../config/object_mapper.php',
            'app'           => ['timezone' => 'UTC'],
        ]));
        $app->instance('request', Request::create('/', 'POST', ['id' => 7, 'name' => 'John']));

        (new ObjectMapperServiceProvider($app))->register();

        $dto = $app->make(RequestDtoClass::class);

        $this->assertInstanceOf(RequestDtoClass::class, $dto);
        $this->assertSame(7, $dto->id);
        $this->assertSame('John', $dto->name);

        Container::setInstance(null);
    }
}
