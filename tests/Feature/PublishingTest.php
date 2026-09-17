<?php

namespace Datalogix\ErrorPages\Tests\Feature;

use Datalogix\ErrorPages\Tests\TestCase;
use Illuminate\Support\Facades\File;

class PublishingTest extends TestCase
{
    protected function tearDown(): void
    {
        File::delete(config_path('error-pages.php'));
        File::deleteDirectory(lang_path('vendor/error-pages'));

        parent::tearDown();
    }

    public function test_config_can_be_published(): void
    {
        $this->artisan('vendor:publish', ['--tag' => 'error-pages-config', '--force' => true]);

        $this->assertFileExists(config_path('error-pages.php'));
    }

    public function test_lang_files_can_be_published(): void
    {
        $this->artisan('vendor:publish', ['--tag' => 'error-pages-lang', '--force' => true]);

        $this->assertFileExists(lang_path('vendor/error-pages/en/messages.php'));
        $this->assertFileExists(lang_path('vendor/error-pages/pt_BR/messages.php'));
    }
}
