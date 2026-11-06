<?php

test('disabled config lets exception pass through', function () {
    config()->set('error-pages.enabled', false);

    $response = $this->get('/boom/404');

    $response->assertStatus(404);
    $response->assertDontSee('class="badge"', false);
});

test('codes restriction only intercepts listed codes', function () {
    config()->set('error-pages.codes', [403]);

    $this->get('/boom/403')->assertStatus(403)->assertSee('class="badge"', false);
    $this->get('/boom/404')->assertStatus(404)->assertDontSee('class="badge"', false);
});

test('config changes at runtime are respected', function () {
    $this->get('/boom/404')->assertSee('class="badge"', false);

    config()->set('error-pages.enabled', false);

    $this->get('/boom/404')->assertDontSee('class="badge"', false);
});

test('exception message is hidden by default', function () {
    $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

    $response = $this->get('/boom-message');

    $response->assertStatus(403);
    $response->assertSee($messages[403]['description']);
    $response->assertDontSee('Upgrade your plan to access this');
});

test('exception message replaces description when enabled', function () {
    $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

    config()->set('error-pages.show_exception_message', true);

    $response = $this->get('/boom-message');

    $response->assertStatus(403);
    $response->assertSee($messages[403]['title']);
    $response->assertSee('Upgrade your plan to access this');
    $response->assertDontSee($messages[403]['description']);
});

test('empty exception message keeps translated description', function () {
    $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

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
