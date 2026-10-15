# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

- `composer test` — run the PHPUnit suite (also writes `clover.xml` and a coverage report)
- `vendor/bin/phpunit --no-coverage --filter test_default_action_links_to_home` — run a single test
- `vendor/bin/phpunit --no-coverage tests/Feature/ActionButtonTest.php` — run a single file
- `composer lint` — format with Laravel Pint

CI runs the shared `datalogix/workflows` `laravel-tests.yml@v1` workflow (Laravel 12 and 13).

## Architecture

The package is a single service provider, `src/ErrorPagesServiceProvider.php`. It registers a `renderable` callback on the app's `ExceptionHandler` for `HttpExceptionInterface`. That callback holds all of the runtime logic:

1. It returns early, so Laravel's default handling runs, when the package is disabled, the request expects JSON, the status isn't in `error-pages.codes` (when set), or the app has its own `errors.{code}` view.
2. It resolves the copy from `lang/{locale}/messages.php`, keyed by status code with a `default` entry. If the current locale (and its fallback) has no translation, it uses `en`. If a code has no entry, it uses `default`.
3. `resolveAction()` picks the button: none for 503, the `login` route for 401 (only when that route exists), the previous URL for 419, and home for everything else.
4. View selection: when `TALLKit\TALLKitServiceProvider` is loaded, it renders `error-pages::tallkit` (`<tk:*>` components). If rendering throws, it calls `report()` and falls back to `error-pages::fallback`, which has no dependencies. It always passes the exception's headers through.

TALLKit is only a `suggest` dependency, so the provider references its class name without requiring the package.

## Tests

- `tests/TestCase.php` extends Orchestra Testbench. It sets `app.key`, points `view.paths` to `tests/Fixtures/app-views` (which has an `errors/418.blade.php` app override), and defines the `/boom/*` routes that trigger exceptions.
- TALLKit is simulated with `tests/Fixtures/TALLKitServiceProviderStub.php` (a stub `TALLKit\TALLKitServiceProvider`). The tests then use `View::prependNamespace('error-pages', ...)` to swap in `Fixtures/views` (a renderable stand-in for the tallkit view) or `Fixtures/broken-views` (a view that throws).
- Tests read expected copy straight from `lang/en/messages.php` instead of hardcoding strings.
