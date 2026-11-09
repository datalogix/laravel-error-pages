<?php

use Illuminate\Support\Arr;

test('every locale has the same message keys as english', function (string $file) {
    $en = array_keys(Arr::dot(require dirname(__DIR__, 2).'/lang/en/messages.php'));
    $keys = array_keys(Arr::dot(require $file));

    expect($keys)->toEqualCanonicalizing($en);
})->with(fn () => glob(dirname(__DIR__, 2).'/lang/*/messages.php'));
