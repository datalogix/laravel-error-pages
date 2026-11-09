<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\View;
use Illuminate\View\ViewException;
use TALLKit\TALLKitServiceProvider;

require_once __DIR__.'/../Fixtures/TALLKitServiceProviderStub.php';

test('tallkit view is selected and rendered when tallkit provider is loaded', function () {
    $this->app->register(TALLKitServiceProvider::class);
    View::prependNamespace('error-pages', __DIR__.'/../Fixtures/views');

    $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

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

    $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

    $response = $this->get('/boom/500');

    $response->assertStatus(500);
    $response->assertSee('class="badge"', false);
    $response->assertSee($messages[500]['title']);

    Exceptions::assertReported(fn (ViewException $e) => str_contains($e->getMessage(), 'TALLKit layout failed to render'));
});
