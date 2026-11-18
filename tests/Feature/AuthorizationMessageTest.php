<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Gate::define('deny-with-message', fn (?Authenticatable $user = null) => Response::deny('Download limit reached'));
    Gate::define('deny-without-message', fn (?Authenticatable $user = null) => false);
    Gate::define('deny-with-status', fn (?Authenticatable $user = null) => Response::denyWithStatus(404, 'This document is gone'));

    Route::get('/gate/{ability}', fn (string $ability) => Gate::authorize($ability));
});

test('policy denial message replaces description', function () {
    $messages = messages();

    $response = $this->get('/gate/deny-with-message');

    $response->assertStatus(403);
    $response->assertSee($messages[403]['title']);
    $response->assertSee('Download limit reached');
    $response->assertDontSee($messages[403]['description']);
});

test('policy denial message is used for custom statuses', function () {
    $messages = messages();

    $response = $this->get('/gate/deny-with-status');

    $response->assertStatus(404);
    $response->assertSee($messages[404]['title']);
    $response->assertSee('This document is gone');
});

test('denial without message keeps translated description', function () {
    $messages = messages();

    $response = $this->get('/gate/deny-without-message');

    $response->assertStatus(403);
    $response->assertSee($messages[403]['description']);
    $response->assertDontSee('This action is unauthorized.');
});

test('policy denial message is hidden when disabled', function () {
    $messages = messages();

    config()->set('error-pages.show_authorization_message', false);

    $response = $this->get('/gate/deny-with-message');

    $response->assertStatus(403);
    $response->assertSee($messages[403]['description']);
    $response->assertDontSee('Download limit reached');
});

test('policy denial message takes precedence over exception message', function () {
    config()->set('error-pages.show_exception_message', true);

    $this->get('/gate/deny-with-message')->assertSee('Download limit reached');
    $this->get('/gate/deny-without-message')->assertSee('This action is unauthorized.');
});

test('manually thrown authorization exception keeps translated description', function () {
    $messages = messages();

    Route::get('/manual-denial', fn () => throw new AuthorizationException('Internal reason'));

    $response = $this->get('/manual-denial');

    $response->assertStatus(403);
    $response->assertSee($messages[403]['description']);
    $response->assertDontSee('Internal reason');
});

test('policy denial message is shown only for listed codes', function (mixed $codes) {
    $messages = messages();

    config()->set('error-pages.show_authorization_message', $codes);

    $this->get('/gate/deny-with-message')->assertSee('Download limit reached');

    $response = $this->get('/gate/deny-with-status');

    $response->assertStatus(404);
    $response->assertSee($messages[404]['description']);
    $response->assertDontSee('This document is gone');
})->with([
    'array' => [[403]],
    'string' => ['403'],
    'integer' => [403],
]);
