## datalogix/laravel-error-pages

Renders translated HTML pages for HTTP exceptions (`abort(404)`, `abort(403)`, ...). It is auto-registered, needs no setup, and uses TALLKit components when `datalogix/tallkit` is loaded.

- Requests that expect JSON are never affected.
- An app view `resources/views/errors/{code}.blade.php` replaces the package page for that code; `errors/4xx` and `errors/5xx` do not.
- Copy lives in `error-pages::messages.{code}` (`title`, `description`) with a `default` entry; publish it with `php artisan vendor:publish --tag=error-pages-lang`.
- Action button: none on 503, the `login` route on 401 (when defined), the previous URL on 419, home otherwise.

@verbatim
<code-snippet name="Configure error pages" lang="php">
// config/error-pages.php (php artisan vendor:publish --tag=error-pages-config)
return [
    'enabled' => true,
    'codes' => [403, 404, 500], // null handles every HTTP exception
    'show_exception_message' => false, // true shows abort(403, 'message') to end users
];
</code-snippet>
@endverbatim
