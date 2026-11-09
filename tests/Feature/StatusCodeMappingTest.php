<?php

test('mapped status code uses its own copy', function (int $code) {
    $messages = messages();

    $response = $this->get("/boom/{$code}");

    $response->assertStatus($code);
    $response->assertSee($messages[$code]['title']);
    $response->assertSee($messages[$code]['description']);
})->with('mappedStatusCodes');

dataset('mappedStatusCodes', fn () => array_filter(array_keys(messages()), is_int(...)));

test('unmapped status code falls back to default copy', function () {
    $messages = messages();

    $response = $this->post('/only-get');

    $response->assertStatus(405);
    $response->assertSee($messages['default']['title']);
    $response->assertSee($messages['default']['description']);
});
