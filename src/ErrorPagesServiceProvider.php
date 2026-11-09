<?php

declare(strict_types=1);

namespace Datalogix\ErrorPages;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use TALLKit\TALLKitServiceProvider;
use Throwable;

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

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/error-pages.php' => config_path('error-pages.php'),
            ], 'error-pages-config');

            $this->publishes([
                __DIR__.'/../lang' => lang_path('vendor/error-pages'),
            ], 'error-pages-lang');
        }

        $this->bootRenderable();
    }

    protected function bootRenderable()
    {
        $handler = $this->app->make(ExceptionHandler::class);

        if (! method_exists($handler, 'renderable')) {
            return;
        }

        $handler->renderable(function (HttpExceptionInterface $e, Request $request) {
            if (! config('error-pages.enabled', true) || $request->expectsJson()) {
                return;
            }

            $code = $e->getStatusCode();
            $codes = config('error-pages.codes');

            if (is_array($codes) && ! in_array($code, $codes)) {
                return;
            }

            if (view()->exists("errors.{$code}")) {
                return;
            }

            $locale = Arr::first(
                [app()->getLocale(), Lang::getFallback()],
                fn (string $locale) => Lang::hasForLocale('error-pages::messages.default', $locale),
            ) ?? 'en';
            $key = Lang::has("error-pages::messages.{$code}", $locale) ? $code : 'default';
            $copy = trans("error-pages::messages.{$key}", [], $locale);

            $description = config('error-pages.show_exception_message', false) && $e->getMessage() !== ''
                ? $e->getMessage()
                : $copy['description'];

            $data = [
                'code' => $code,
                'locale' => $locale,
                'title' => $copy['title'],
                'description' => $description,
                'action' => $this->resolveAction($code, $locale),
            ];

            if ($this->app->providerIsLoaded(TALLKitServiceProvider::class)) {
                try {
                    return response()->view('error-pages::tallkit', $data, $code, $e->getHeaders());
                } catch (Throwable $renderException) {
                    report($renderException);
                }
            }

            return response()->view('error-pages::fallback', $data, $code, $e->getHeaders());
        });
    }

    /**
     * @return array{url: string, label: string}|null
     */
    protected function resolveAction(int $code, string $locale): ?array
    {
        $action = fn (string $url, string $label) => [
            'url' => $url,
            'label' => trans("error-pages::messages.{$label}", [], $locale),
        ];

        return match (true) {
            $code === 503 => null,
            $code === 401 && Route::has('login') => $action(route('login'), 'sign_in'),
            $code === 419 => $action(url()->previous(), 'go_back'),
            default => $action(url('/'), 'back_home'),
        };
    }
}
