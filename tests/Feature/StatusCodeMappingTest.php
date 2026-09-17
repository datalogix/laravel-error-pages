<?php

namespace Datalogix\ErrorPages\Tests\Feature;

use Datalogix\ErrorPages\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class StatusCodeMappingTest extends TestCase
{
    #[DataProvider('mappedStatusCodes')]
    public function test_mapped_status_code_uses_its_own_copy(int $code): void
    {
        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        $response = $this->get("/boom/{$code}");

        $response->assertStatus($code);
        $response->assertSee($messages[$code]['title']);
        $response->assertSee($messages[$code]['description']);
    }

    public static function mappedStatusCodes(): array
    {
        return [[400], [401], [403], [419], [429], [500], [502], [503], [504]];
    }

    public function test_unmapped_status_code_falls_back_to_default_copy(): void
    {
        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        $response = $this->post('/only-get');

        $response->assertStatus(405);
        $response->assertSee($messages['default']['title']);
        $response->assertSee($messages['default']['description']);
    }
}
