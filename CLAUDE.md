# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

- `npm install` — install Prettier and its Blade/Tailwind plugins (required by `pint --blade`)
- `composer test` — run the Pest suite (also writes `clover.xml` and a coverage report)
- `vendor/bin/pest --no-coverage --filter "default action links to home"` — run a single test
- `vendor/bin/pest --no-coverage tests/Feature/ActionButtonTest.php` — run a single file
- `composer lint` — check only: Pint (`--test --blade`), Larastan and Rector (`--dry-run`)
- `composer format` — format PHP and Blade with Pint (`--blade`)

CI runs `.github/workflows/datalogix.yml`, which calls the shared `datalogix/workflows` `package.yml@v2` pipeline.

- Issues and pull requests: English

## Architecture

The package is a single service provider, `src/ErrorPagesServiceProvider.php`. It registers a `renderable` callback on the app's `ExceptionHandler` for `HttpExceptionInterface`; `bootRenderable()` skips registration when the bound handler has no `renderable()` method. That callback holds all of the runtime logic:

1. It returns early, so Laravel's default handling runs, when the package is disabled, the request expects JSON, the status isn't in `error-pages.codes` (an array, or a comma-separated string or integer; `null` or any other value handles every code), or the app has its own `errors.{code}` view.
2. It resolves the copy from `lang/{locale}/messages.php`, keyed by status code with a `default` entry. It tries the current locale, then the app's fallback locale, then `en`. If a code has no entry, it uses `default`.
3. `resolveAction()` picks the button: none for 503, the `login` route for 401 (only when that route exists), the previous URL for 419, and home for everything else.
4. View selection: when `TALLKit\TALLKitServiceProvider` is loaded, it renders `error-pages::tallkit` (`<tk:*>` components). If rendering throws, it calls `report()` and falls back to `error-pages::fallback`, which has no dependencies. It always passes the exception's headers through. Both views receive the same data (`code`, `locale`, `title`, `description`, `action`); the tallkit view disables TALLKit's own home button (`:home-url="false"`) and renders the package action instead.

TALLKit is only a `suggest` dependency (and a `require-dev` one for tests), so the provider references its class name without requiring the package at runtime.

## Tests

- Pest, bound to `tests/TestCase.php` through `uses()` in `tests/Pest.php`. `TestCase` extends Orchestra Testbench, loads `ErrorPagesServiceProvider` and `TailwindMergeServiceProvider`, sets `app.key`, points `view.paths` to `tests/Fixtures/app-views` (which has an `errors/418.blade.php` app override), and defines the `/boom/*` routes that trigger exceptions.
- TALLKit tests use the real `datalogix/tallkit` (require-dev): they register `TALLKit\TALLKitServiceProvider` per test and call `Http::fake()` so icon fetching never hits the network. To isolate selection and fallback, they use `View::prependNamespace('error-pages', ...)` to swap in `Fixtures/views` (a stand-in for the tallkit view) or `Fixtures/broken-views` (a view that throws).
- Tests read expected copy straight from `lang/{locale}/messages.php` instead of hardcoding strings; `TranslationsTest` checks that every locale has the same keys as `en`.
