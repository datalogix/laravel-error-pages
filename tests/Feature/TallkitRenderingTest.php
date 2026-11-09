<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\View\ViewException;
use TALLKit\TALLKitServiceProvider;

beforeEach(fn () => Http::fake());

test('tallkit view is selected and rendered when tallkit provider is loaded', function () {
    $this->app->register(TALLKitServiceProvider::class);
    View::prependNamespace('error-pages', __DIR__.'/../Fixtures/views');

    $messages = messages();

    $response = $this->get('/boom/404');

    $response->assertStatus(404);
    $response->assertSee('tallkit-fixture', false);
    $response->assertSee($messages[404]['title']);
    $response->assertDontSee('class="badge"', false);
});

test('fallback view is used when tallkit is installed but provider not loaded', function () {
    View::prependNamespace('error-pages', __DIR__.'/../Fixtures/views');

    $response = $this->get('/boom/404');

    $response->assertStatus(404);
    $response->assertSee('class="badge"', false);
    $response->assertDontSee('tallkit-fixture', false);
});

test('fallback view is used and failure reported when tallkit view throws', function () {
    Exceptions::fake();

    $this->app->register(TALLKitServiceProvider::class);
    View::prependNamespace('error-pages', __DIR__.'/../Fixtures/broken-views');

    $messages = messages();

    $response = $this->get('/boom/500');

    $response->assertStatus(500);
    $response->assertSee('class="badge"', false);
    $response->assertSee($messages[500]['title']);

    Exceptions::assertReported(fn (ViewException $e) => str_contains($e->getMessage(), 'TALLKit layout failed to render'));
});

test('real tallkit view renders the package copy and title', function () {
    $this->app->register(TALLKitServiceProvider::class);

    $messages = messages();

    $response = $this->get('/boom/404');

    $response->assertStatus(404);
    $response->assertSee('<title>404 — '.e($messages[404]['title']).'</title>', false);
    $response->assertSee($messages[404]['description']);
    $response->assertSee($messages['back_home']);
    $response->assertDontSee('Go back home');
    $response->assertDontSee('class="badge"', false);
});

test('real tallkit view has no button for 503', function () {
    $this->app->register(TALLKitServiceProvider::class);

    $messages = messages();

    $response = $this->get('/boom/503');

    $response->assertStatus(503);
    $response->assertDontSee($messages['back_home']);
    $response->assertDontSee('Go back home');
    $response->assertDontSee('class="badge"', false);
});

test('real tallkit view keeps the exception headers', function () {
    $this->app->register(TALLKitServiceProvider::class);

    $response = $this->post('/only-get');

    $response->assertStatus(405);
    $response->assertDontSee('class="badge"', false);
    expect($response->headers->get('Allow'))->toContain('GET');
});

test('real tallkit view uses the locale of the copy', function () {
    $this->app->register(TALLKitServiceProvider::class);

    $ptBr = messages('pt_BR');

    App::setLocale('pt_BR');

    $response = $this->get('/boom/404');

    $response->assertSee($ptBr[404]['title']);
    $response->assertSee($ptBr['back_home']);
    $response->assertDontSee('class="badge"', false);
});
