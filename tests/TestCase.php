<?php

namespace Datalogix\ErrorPages\Tests;

use Datalogix\ErrorPages\ErrorPagesServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;
use RuntimeException;

abstract class TestCase extends AbstractPackageTestCase
{
    protected static function getServiceProviderClass(): string
    {
        return ErrorPagesServiceProvider::class;
    }

    protected function defineEnvironment($app): void
    {
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
