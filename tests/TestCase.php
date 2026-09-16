<?php

namespace Datalogix\ErrorPages\Tests;

use Datalogix\ErrorPages\ErrorPagesServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;
use RuntimeException;

abstract class TestCase extends AbstractPackageTestCase
{
    /**
     * Config values merged into the app before the providers boot. Needs a
     * refreshApplication() call after setting this, since bootRenderable()
     * reads error-pages.enabled/codes once, at boot time.
     *
     * @var array<string, mixed>
     */
    protected array $errorPagesConfig = [];

    protected static function getServiceProviderClass(): string
    {
        return ErrorPagesServiceProvider::class;
    }

    protected function defineEnvironment($app): void
    {
        foreach ($this->errorPagesConfig as $key => $value) {
            $app['config']->set($key, $value);
        }
    }

    protected function defineWebRoutes($router): void
    {
        $router->get('/boom/{code}', fn (int $code) => abort($code));
        $router->get('/boom-headers', fn () => abort(419, '', ['X-Test' => 'header-value']));
        $router->get('/boom-runtime', fn () => throw new RuntimeException('not an http exception'));
        $router->get('/only-get', fn () => 'ok');
    }
}
