<?php

namespace Datalogix\ErrorPages\Tests\Feature;

use Datalogix\ErrorPages\Tests\TestCase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;

class PublishingTest extends TestCase
{
    protected function tearDown(): void
    {
        File::delete(config_path('error-pages.php'));
        File::deleteDirectory(lang_path('vendor/error-pages'));

        parent::tearDown();
    }

    #[Test]
    public function config_can_be_published(): void
    {
        $this->artisan('vendor:publish', ['--tag' => 'error-pages-config', '--force' => true]);

        $this->assertFileExists(config_path('error-pages.php'));
    }

    #[Test]
    public function lang_files_can_be_published(): void
    {
        $this->artisan('vendor:publish', ['--tag' => 'error-pages-lang', '--force' => true]);

        $this->assertFileExists(lang_path('vendor/error-pages/en/messages.php'));
        $this->assertFileExists(lang_path('vendor/error-pages/pt_BR/messages.php'));
    }
}
