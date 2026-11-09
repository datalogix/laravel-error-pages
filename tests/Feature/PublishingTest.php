<?php

use Illuminate\Support\Facades\File;

afterEach(function () {
    File::delete(config_path('error-pages.php'));
    File::deleteDirectory(lang_path('vendor/error-pages'));
});

test('config can be published', function () {
    $this->artisan('vendor:publish', ['--tag' => 'error-pages-config', '--force' => true])->assertSuccessful();

    expect(config_path('error-pages.php'))->toBeFile();
});

test('lang files can be published', function () {
    $this->artisan('vendor:publish', ['--tag' => 'error-pages-lang', '--force' => true])->assertSuccessful();

    expect(lang_path('vendor/error-pages/en/messages.php'))->toBeFile();
    expect(lang_path('vendor/error-pages/pt_BR/messages.php'))->toBeFile();
});
