<?php

namespace Datalogix\ErrorPages\Tests;

use Datalogix\ErrorPages\ErrorPagesServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use RuntimeException;
use TailwindMerge\Laravel\TailwindMergeServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return array_filter([ErrorPagesServiceProvider::class, TailwindMergeServiceProvider::class], class_exists(...));
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('view.paths', [__DIR__.'/Fixtures/app-views']);
    }

    protected function defineWebRoutes($router): void
    {
        $router->get('/boom/{code}', fn (int $code) => abort($code));
        $router->get('/boom-headers', fn () => abort(419, '', ['X-Test' => 'header-value']));
        $router->get('/boom-message', fn () => abort(403, 'Upgrade your plan to access this'));
        $router->get('/boom-runtime', fn () => throw new RuntimeException('not an http exception'));
        $router->get('/only-get', fn () => 'ok');
    }
}
