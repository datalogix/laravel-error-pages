<?php

use Illuminate\Support\Facades\Route;

test('default action links to home', function () {
    $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

    $response = $this->get('/boom/404');

    $response->assertSee('href="'.url('/').'"', false);
    $response->assertSee($messages['back_home']);
});

test('maintenance page has no action', function () {
    $response = $this->get('/boom/503');

    $response->assertStatus(503);
    $response->assertSee('class="badge"', false);
    $response->assertDontSee('class="button"', false);
});

test('unauthorized links to login when route exists', function () {
    $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

    Route::get('/login', fn () => 'login')->name('login');
    Route::getRoutes()->refreshNameLookups();

    $response = $this->get('/boom/401');

    $response->assertSee('href="'.url('/login').'"', false);
    $response->assertSee($messages['sign_in']);
});

test('unauthorized links to home without login route', function () {
    $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

    $response = $this->get('/boom/401');

    $response->assertSee('href="'.url('/').'"', false);
    $response->assertSee($messages['back_home']);
});

test('expired session links back to previous page', function () {
    $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

    $response = $this->from('/checkout')->get('/boom/419');

    $response->assertSee('href="'.url('/checkout').'"', false);
    $response->assertSee($messages['go_back']);
});
