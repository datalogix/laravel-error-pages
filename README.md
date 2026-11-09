# Laravel Error Pages

[![Latest Stable Version](https://poser.pugx.org/datalogix/laravel-error-pages/version)](https://packagist.org/packages/datalogix/laravel-error-pages)
[![Total Downloads](https://poser.pugx.org/datalogix/laravel-error-pages/downloads)](https://packagist.org/packages/datalogix/laravel-error-pages)
[![ci](https://img.shields.io/github/actions/workflow/status/datalogix/laravel-error-pages/datalogix.yml?branch=main&label=ci)](https://github.com/datalogix/laravel-error-pages/actions/workflows/datalogix.yml)
[![codecov](https://codecov.io/gh/datalogix/laravel-error-pages/branch/main/graph/badge.svg)](https://codecov.io/gh/datalogix/laravel-error-pages)
[![License](https://poser.pugx.org/datalogix/laravel-error-pages/license)](https://packagist.org/packages/datalogix/laravel-error-pages)

> Renders custom, translated HTTP error pages (404, 403, 500, ...) for your Laravel application — automatically upgraded to TALLKit components when available.

## Requirements

- PHP 8.2+
- Laravel 12 or 13
- Optional: [`datalogix/tallkit`](https://github.com/datalogix/tallkit) for the TALLKit page

## Installation

You can install the package via composer:

```bash
composer require datalogix/laravel-error-pages
```

The package will automatically register itself.

## Features

These features work out of the box—no additional configuration required:

- 🎨 **Custom Error Pages** — Replaces Laravel's default error pages for any HTTP exception (404, 403, 500, and more) with a clean, translated page, including automatic dark mode support.

- 🌍 **Multi-language Support** — Includes built-in translations for English (`en`) and Brazilian Portuguese (`pt_BR`). Files are auto-loaded and can be published for customization. The copy is resolved in this order: the current locale, then your app's `fallback_locale`, then `en`. The page's `lang` attribute matches the locale actually used.

- 🧭 **Context-aware Action Button** — The button adapts to the error: `401` links to your `login` route (when defined), `419` sends the user back to the previous page, `503` (maintenance) shows no button, and everything else links to the home page. The same rules apply to the fallback and the TALLKit page.

- 🧩 **TALLKit Integration** — Automatically renders richer error pages using [TALLKit](https://github.com/datalogix/tallkit)'s `<tk:layout.error>` and `<tk:page.error>` components when `datalogix/tallkit` is installed, with no configuration required.

- 🔌 **JSON-aware** — Requests that expect JSON are left untouched, so your API responses are never affected.

- 🧱 **Respects Your Own Views** — If your application defines `resources/views/errors/{code}.blade.php`, that view is used instead of the package's page for that status code. Only exact status code views are considered: `errors/4xx.blade.php` and `errors/5xx.blade.php` do not disable the package's page. To skip a code entirely, leave it out of `codes` (see [Configuration](#configuration)).

## Configuration

To publish the config file, run:

```bash
php artisan vendor:publish --provider="Datalogix\ErrorPages\ErrorPagesServiceProvider" --tag="error-pages-config"
```

This will create a `config/error-pages.php` file:

```php
return [
    // Enable or disable the custom error pages entirely.
    'enabled' => true,

    // Restrict rendering to specific status codes, e.g. [403, 404, 500] or "403,404,500".
    // Leave as `null` to handle every HTTP exception.
    'codes' => null,

    // Show the exception message (e.g. abort(403, 'Upgrade your plan'))
    // instead of the translated description when it isn't empty.
    'show_exception_message' => false,
];
```

> ⚠️ When `show_exception_message` is enabled, framework-generated messages are shown too — e.g. `The route foo could not be found.` or `No query results for model [App\Models\User].` Only enable it if your HTTP exception messages are meant for end users.

## Translations

To publish the language files for customization, run:

```bash
php artisan vendor:publish --provider="Datalogix\ErrorPages\ErrorPagesServiceProvider" --tag="error-pages-lang"
```

This will copy the `en` and `pt_BR` translation files to `lang/vendor/error-pages`, where you can edit the existing copy or add new locales.

## TALLKit Integration

If [`datalogix/tallkit`](https://github.com/datalogix/tallkit) is installed and its service provider is loaded, error pages are automatically rendered using its `<tk:layout.error>` and `<tk:page.error>` components for a richer, design-system-consistent look. No extra configuration is needed — the package detects TALLKit's presence and switches views automatically.

The TALLKit page shows the package's own copy, action button (no button on `503`, your `login` route on `401`, the previous page on `419`, home otherwise), `<html lang>` and `<title>`, exactly like the fallback page.

If the TALLKit page itself fails to render (e.g. a missing Vite manifest or an unavailable database while handling a `500`), the failure is reported and the dependency-free fallback page is shown instead.

## Contributing

```bash
composer install
npm install # Prettier plugins required by `pint --blade`
```

- `composer test` — run the test suite
- `composer lint` — check formatting (Pint), static analysis (Larastan) and refactors (Rector)
- `composer format` — format PHP and Blade files

## Changelog

See [GitHub Releases](https://github.com/datalogix/laravel-error-pages/releases).

## License

The MIT License (MIT). See [LICENSE](LICENSE) for details.
