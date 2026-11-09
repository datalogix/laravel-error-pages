<?php

use Illuminate\Support\Arr;

test('every locale has the same message keys as english', function (string $locale) {
    expect(array_keys(Arr::dot(messages($locale))))->toEqualCanonicalizing(array_keys(Arr::dot(messages())));
})->with(fn () => array_map(fn (string $file) => basename(dirname($file)), glob(dirname(__DIR__, 2).'/lang/*/messages.php')));
