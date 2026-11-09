<?php

use Illuminate\Support\Facades\App;

test('fallback view renders for an http exception', function () {
    $messages = messages();

    $response = $this->get('/boom/403');

    $response->assertStatus(403);
    $response->assertSee('class="badge"', false);
    $response->assertSee($messages[403]['title']);
    $response->assertSee($messages[403]['description']);
    $response->assertSee($messages['back_home']);
});

test('fallback view uses locale specific copy', function () {
    $en = messages();
    $ptBr = messages('pt_BR');

    App::setLocale('pt_BR');

    $response = $this->get('/boom/403');

    $response->assertStatus(403);
    $response->assertSee($ptBr[403]['title']);
    $response->assertSee($ptBr[403]['description']);
    $response->assertDontSee($en[403]['title']);
});

test('untranslated locale resolves copy through the fallback chain', function (?string $fallback, string $expected) {
    App::setLocale('fr');
    app('translator')->setFallback($fallback);

    $response = $this->get('/boom/404');

    $response->assertStatus(404);
    $response->assertSee('<html lang="'.str_replace('_', '-', $expected).'">', false);
    $response->assertSee(messages($expected)[404]['title']);
    $response->assertSee(messages($expected)['back_home']);
})->with([
    'english fallback' => ['en', 'en'],
    'non english fallback' => ['pt_BR', 'pt_BR'],
    'untranslated fallback' => ['fr', 'en'],
    'no fallback' => [null, 'en'],
]);

test('untranslated code uses the default copy of the chosen locale', function () {
    $en = messages();

    app('translator')->addLines([
        'messages.default.title' => 'Algo salió mal',
        'messages.default.description' => 'Inténtalo de nuevo.',
        'messages.back_home' => 'Volver al inicio',
    ], 'es', 'error-pages');
    App::setLocale('es');

    $response = $this->get('/boom/404');

    $response->assertStatus(404);
    $response->assertSee('<html lang="es">', false);
    $response->assertSee('Algo salió mal');
    $response->assertSee('Volver al inicio');
    $response->assertDontSee($en[404]['title']);
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
