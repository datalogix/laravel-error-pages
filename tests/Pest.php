<?php

use Datalogix\ErrorPages\Tests\TestCase;

uses(TestCase::class)->in('Feature');

function messages(string $locale = 'en'): array
{
    return require __DIR__.'/../lang/'.$locale.'/messages.php';
}
