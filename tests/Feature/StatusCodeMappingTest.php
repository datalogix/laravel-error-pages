<?php

test('mapped status code uses its own copy', function (int $code) {
    $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

    $response = $this->get("/boom/{$code}");

    $response->assertStatus($code);
    $response->assertSee($messages[$code]['title']);
    $response->assertSee($messages[$code]['description']);
})->with('mappedStatusCodes');

dataset('mappedStatusCodes', fn () => [[400], [401], [403], [404], [419], [429], [500], [502], [503], [504]]);

test('unmapped status code falls back to default copy', function () {
    $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

    $response = $this->post('/only-get');

    $response->assertStatus(405);
    $response->assertSee($messages['default']['title']);
    $response->assertSee($messages['default']['description']);
});
