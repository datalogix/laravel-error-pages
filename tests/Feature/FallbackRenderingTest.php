<?php

use Illuminate\Support\Facades\App;

test('fallback view renders for an http exception', function () {
    $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

    $response = $this->get('/boom/403');

    $response->assertStatus(403);
    $response->assertSee('class="badge"', false);
    $response->assertSee($messages[403]['title']);
    $response->assertSee($messages[403]['description']);
    $response->assertSee($messages['back_home']);
});

test('fallback view uses locale specific copy', function () {
    $en = require dirname(__DIR__, 2).'/lang/en/messages.php';
    $ptBr = require dirname(__DIR__, 2).'/lang/pt_BR/messages.php';

    App::setLocale('pt_BR');

    $response = $this->get('/boom/403');

    $response->assertStatus(403);
    $response->assertSee($ptBr[403]['title']);
    $response->assertSee($ptBr[403]['description']);
    $response->assertDontSee($en[403]['title']);
});

test('untranslated locale falls back to english copy', function () {
    $en = require dirname(__DIR__, 2).'/lang/en/messages.php';

    App::setLocale('es');
    app('translator')->setFallback('es');

    $response = $this->get('/boom/404');

    $response->assertStatus(404);
    $response->assertSee('<html lang="en">', false);
    $response->assertSee($en[404]['title']);
    $response->assertSee($en['back_home']);
});

test('original exception headers are preserved', function () {
    $response = $this->get('/boom-headers');

    $response->assertStatus(419);
    $response->assertHeader('X-Test', 'header-value');
});

test('router allow header is preserved via method not allowed', function () {
    $response = $this->post('/only-get');

    $response->assertStatus(405);
    $response->assertHeader('Allow');
    expect($response->headers->get('Allow'))->toContain('GET');
});
