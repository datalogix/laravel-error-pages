<?php

namespace Datalogix\ErrorPages;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use TALLKit\TALLKitServiceProvider;

class ErrorPagesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/error-pages.php', 'error-pages');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'error-pages');
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'error-pages');

        $this->publishes([
            __DIR__.'/../config/error-pages.php' => config_path('error-pages.php'),
        ], 'error-pages-config');

        $this->publishes([
            __DIR__.'/../lang' => lang_path('vendor/error-pages'),
        ], 'error-pages-lang');

        $this->bootRenderable();
    }

    protected function bootRenderable()
    {
        if (! config('error-pages.enabled', true)) {
            return;
        }

        $codes = config('error-pages.codes');

        app(ExceptionHandler::class)->renderable(function (HttpExceptionInterface $e, $request) use ($codes) {
            if ($request->expectsJson()) {
                return;
            }

            if ($codes !== null && ! in_array($e->getStatusCode(), $codes)) {
                return;
            }

            $code = $e->getStatusCode();
            $messages = trans('error-pages::messages');
            $copy = $messages[$code] ?? $messages['default'];

            $view = class_exists(TALLKitServiceProvider::class)
                ? 'error-pages::tallkit'
                : 'error-pages::fallback';

            return response()->view($view, [
                'code' => $code,
                'title' => $copy['title'],
                'description' => $copy['description'],
            ], $code, $e->getHeaders());
        });
    }
}
