<?php

use Datalogix\ErrorPages\ErrorPagesServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use TALLKit\TALLKitServiceProvider;

test('disabled config lets exception pass through', function () {
    config()->set('error-pages.enabled', false);

    $response = $this->get('/boom/404');

    $response->assertStatus(404);
    $response->assertDontSee('class="badge"', false);
});

test('codes config only intercepts listed codes', function (mixed $codes) {
    config()->set('error-pages.codes', $codes);

    $this->get('/boom/404')->assertStatus(404)->assertSee('class="badge"', false);
    $this->get('/boom/500')->assertStatus(500)->assertDontSee('class="badge"', false);
})->with([
    'array' => [[403, 404]],
    'string array' => [['403', '404']],
    'comma string' => ['403, 404'],
    'integer' => [404],
]);

test('blank string or non array codes config intercepts every code', function (mixed $codes) {
    config()->set('error-pages.codes', $codes);

    $this->get('/boom/404')->assertStatus(404)->assertSee('class="badge"', false);
})->with(['', ' ', true]);

test('empty codes array intercepts no code', function () {
    config()->set('error-pages.codes', []);

    $this->get('/boom/404')->assertStatus(404)->assertDontSee('class="badge"', false);
});

test('config changes at runtime are respected', function () {
    $this->get('/boom/404')->assertSee('class="badge"', false);

    config()->set('error-pages.enabled', false);

    $this->get('/boom/404')->assertDontSee('class="badge"', false);
});

test('exception message is hidden by default', function () {
    $messages = messages();

    $response = $this->get('/boom-message');

    $response->assertStatus(403);
    $response->assertSee($messages[403]['description']);
    $response->assertDontSee('Upgrade your plan to access this');
});

test('exception message replaces description when enabled', function () {
    $messages = messages();

    config()->set('error-pages.show_exception_message', true);

    $response = $this->get('/boom-message');

    $response->assertStatus(403);
    $response->assertSee($messages[403]['title']);
    $response->assertSee('Upgrade your plan to access this');
    $response->assertDontSee($messages[403]['description']);
});

test('exception message is escaped', function (bool $tallkit) {
    if ($tallkit) {
        Http::fake();
        $this->app->register(TALLKitServiceProvider::class);
    }

    Route::get('/boom-html', fn () => abort(403, '<script>alert(1)</script>'));
    config()->set('error-pages.show_exception_message', true);

    $response = $this->get('/boom-html');

    $response->assertStatus(403);
    $response->assertDontSee('<script>alert(1)</script>', false);
    $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
})->with(['fallback' => false, 'tallkit' => true]);

test('empty exception message keeps translated description', function () {
    $messages = messages();

    config()->set('error-pages.show_exception_message', true);

    $this->get('/boom/403')->assertSee($messages[403]['description']);
});

test('application error view takes precedence', function () {
    $response = $this->get('/boom/418');

    $response->assertStatus(418);
    $response->assertSee('App-defined 418 page');
    $response->assertDontSee('class="badge"', false);

    $this->get('/boom/404')->assertSee('class="badge"', false);
});

test('handlers without renderable are left untouched', function () {
    $this->app->instance(ExceptionHandler::class, new class implements ExceptionHandler
    {
        public function report(Throwable $e) {}

        public function shouldReport(Throwable $e)
        {
            return true;
        }

        public function render($request, Throwable $e) {}

        public function renderForConsole($output, Throwable $e) {}
    });

    expect(fn () => (new ErrorPagesServiceProvider($this->app))->boot())->not->toThrow(Throwable::class);
});

test('json requests bypass the package', function () {
    $response = $this->getJson('/boom/404');

    $response->assertStatus(404);
    $response->assertDontSee('class="badge"', false);
    $response->assertJsonStructure(['message']);
});

test('non http exceptions are not touched', function () {
    $response = $this->get('/boom-runtime');

    $response->assertStatus(500);
    $response->assertDontSee('class="badge"', false);
});
