<?php

namespace Shureban\LaravelObjectMapper\Tests;

use Closure;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Shureban\LaravelObjectMapper\Support\ClassMetadata;
use Throwable;

/**
 * Standalone test case: boots a minimal container with the package config,
 * so the suite runs without a full Laravel application (CI, package checkout).
 */
abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container();
        $container->instance('config', new Repository([
            'object_mapper' => require __DIR__ . '/../config/object_mapper.php',
            'app'           => ['timezone' => 'UTC'],
        ]));

        Container::setInstance($container);
        ClassMetadata::flush();
    }

    protected function tearDown(): void
    {
        Container::setInstance(null);

        parent::tearDown();
    }

    /**
     * @param Closure      $callback
     * @param class-string $expectedException
     *
     * @return void
     */
    protected function assertThrows(Closure $callback, string $expectedException): void
    {
        try {
            $callback();
        } catch (Throwable $exception) {
            $this->assertInstanceOf($expectedException, $exception);

            return;
        }

        $this->fail(sprintf('Expected exception %s was not thrown', $expectedException));
    }
}
