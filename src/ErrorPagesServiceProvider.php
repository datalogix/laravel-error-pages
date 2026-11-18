<?php

namespace Datalogix\ErrorPages;

use Illuminate\Auth\Access\AuthorizationException;
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
    }

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'error-pages');
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
            $codes = $this->parseCodes(config('error-pages.codes'));

            if ($codes !== null && ! in_array($code, $codes, true)) {
                return;
            }

            if (view()->exists("errors.{$code}")) {
                return;
            }

            $locale = Arr::first(
                array_filter([app()->getLocale(), Lang::getFallback()]),
                fn (string $locale) => Lang::hasForLocale('error-pages::messages.default', $locale),
            ) ?? 'en';
            $key = Lang::hasForLocale("error-pages::messages.{$code}", $locale) ? $code : 'default';
            $copy = trans("error-pages::messages.{$key}", [], $locale);

            $authorizationMessage = $this->resolveAuthorizationMessage($e);

            $description = match (true) {
                $authorizationMessage !== null && $this->enabledForCode(config('error-pages.show_authorization_message', true), $code) => $authorizationMessage,
                $e->getMessage() !== '' && $this->enabledForCode(config('error-pages.show_exception_message', false), $code) => $e->getMessage(),
                default => $copy['description'],
            };

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
     * @return list<int>|null
     */
    protected function parseCodes(mixed $value): ?array
    {
        if (is_int($value) || (is_string($value) && trim($value) !== '')) {
            return array_map(intval(...), explode(',', (string) $value));
        }

        return is_array($value) ? array_values(array_map(intval(...), $value)) : null;
    }

    protected function enabledForCode(mixed $value, int $code): bool
    {
        if (is_bool($value) || in_array($value, [0, 1, '0', '1'], true)) {
            return (bool) $value;
        }

        return in_array($code, $this->parseCodes($value) ?? [], true);
    }

    protected function resolveAuthorizationMessage(HttpExceptionInterface $e): ?string
    {
        $previous = $e->getPrevious();

        if (! $previous instanceof AuthorizationException) {
            return null;
        }

        return $previous->response()?->message() ?: null;
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
