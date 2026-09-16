# Laravel Error Pages

[![Latest Stable Version](https://poser.pugx.org/datalogix/laravel-error-pages/version)](https://packagist.org/packages/datalogix/laravel-error-pages)
[![Total Downloads](https://poser.pugx.org/datalogix/laravel-error-pages/downloads)](https://packagist.org/packages/datalogix/laravel-error-pages)
[![tests](https://github.com/datalogix/laravel-error-pages/workflows/tests/badge.svg)](https://github.com/datalogix/laravel-error-pages/actions)
[![StyleCI](https://github.styleci.io/repos/1373546794/shield?style=flat)](https://github.styleci.io/repos/1373546794)
[![codecov](https://codecov.io/gh/datalogix/laravel-error-pages/branch/main/graph/badge.svg)](https://codecov.io/gh/datalogix/laravel-error-pages)
[![License](https://poser.pugx.org/datalogix/laravel-error-pages/license)](https://packagist.org/packages/datalogix/laravel-error-pages)

> Renders custom, translated HTTP error pages (404, 403, 500, ...) for your Laravel application — automatically upgraded to TALLKit components when available.

## Installation

You can install the package via composer:

```bash
composer require datalogix/laravel-error-pages
```

The package will automatically register itself.

## ✅ Features

These features work out of the box—no additional configuration required:

- 🎨 **Custom Error Pages**
  Replaces Laravel's default error pages for any HTTP exception (404, 403, 500, and more) with a clean, translated page — including automatic dark mode support.

- 🌍 **Multi-language Support**
  Includes built-in translations for English (`en`) and Brazilian Portuguese (`pt_BR`). Files are auto-loaded and can be published for customization.

- 🧩 **TALLKit Integration**
  Automatically renders richer error pages using [TALLKit](https://github.com/datalogix/tallkit)'s `<tk:layout.error>` and `<tk:page.error>` components when `datalogix/tallkit` is installed — no configuration required.

- 🔌 **JSON-aware**
  Requests that expect JSON are left untouched, so your API responses are never affected.

- 🛠️ **Console Support**
  Config and translation files can be published using Artisan when running in the console environment.

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

    // Restrict rendering to specific status codes, e.g. [403, 404, 500].
    // Leave as `null` to handle every HTTP exception.
    'codes' => null,
];
```

## Translations

To publish the language files for customization, run:

```bash
php artisan vendor:publish --provider="Datalogix\ErrorPages\ErrorPagesServiceProvider" --tag="error-pages-lang"
```

This will copy the `en` and `pt_BR` translation files to `lang/vendor/error-pages`, where you can edit the existing copy or add new locales.

## TALLKit Integration

If [`datalogix/tallkit`](https://github.com/datalogix/tallkit) is installed in your application, error pages are automatically rendered using its `<tk:layout.error>` and `<tk:page.error>` components for a richer, design-system-consistent look. No extra configuration is needed — the package detects TALLKit's presence and switches views automatically.
