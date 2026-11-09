<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use TALLKit\TALLKitServiceProvider;

test('action points to the expected url', function (bool $tallkit, string $uri, ?string $referer, bool $login, string $url, string $label) {
    if ($tallkit) {
        Http::fake();
        $this->app->register(TALLKitServiceProvider::class);
    }

    if ($login) {
        Route::get('/login', fn () => 'login')->name('login');
    }

    $response = $referer ? $this->from($referer)->get($uri) : $this->get($uri);

    $response->assertSee('href="'.url($url).'"', false);
    $response->assertSee(messages()[$label]);
    $tallkit ? $response->assertDontSee('class="badge"', false) : $response->assertSee('class="badge"', false);
})->with(['fallback' => false, 'tallkit' => true])->with([
    'home' => ['/boom/404', null, false, '/', 'back_home'],
    'login' => ['/boom/401', null, true, '/login', 'sign_in'],
    '401 without login' => ['/boom/401', null, false, '/', 'back_home'],
    '419 with referer' => ['/boom/419', '/checkout', false, '/checkout', 'go_back'],
    '419 without referer' => ['/boom/419', null, false, '/', 'go_back'],
]);

test('expired session links back to the referer header', function (bool $tallkit) {
    if ($tallkit) {
        Http::fake();
        $this->app->register(TALLKitServiceProvider::class);
    }

    $response = $this->withHeader('referer', url('/checkout'))->get('/boom/419');

    $response->assertSee('href="'.url('/checkout').'"', false);
    $response->assertSee(messages()['go_back']);
})->with(['fallback' => false, 'tallkit' => true]);

test('maintenance page has no action', function () {
    $response = $this->get('/boom/503');

    $response->assertStatus(503);
    $response->assertSee('class="badge"', false);
    $response->assertDontSee('class="button"', false);
});
