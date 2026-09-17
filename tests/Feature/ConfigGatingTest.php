<?php

namespace Datalogix\ErrorPages\Tests\Feature;

use Datalogix\ErrorPages\Tests\TestCase;

class ConfigGatingTest extends TestCase
{
    public function test_disabled_config_lets_exception_pass_through(): void
    {
        $this->errorPagesConfig = ['error-pages.enabled' => false];
        $this->refreshApplication();

        $response = $this->get('/boom/404');

        $response->assertStatus(404);
        $response->assertDontSee('class="badge"', false);
    }

    public function test_codes_restriction_only_intercepts_listed_codes(): void
    {
        $this->errorPagesConfig = ['error-pages.codes' => [403]];
        $this->refreshApplication();

        $this->get('/boom/403')->assertStatus(403)->assertSee('class="badge"', false);
        $this->get('/boom/404')->assertStatus(404)->assertDontSee('class="badge"', false);
    }

    public function test_json_requests_bypass_the_package(): void
    {
        $response = $this->getJson('/boom/404');

        $response->assertStatus(404);
        $response->assertDontSee('class="badge"', false);
        $response->assertJsonStructure(['message']);
    }

    public function test_non_http_exceptions_are_not_touched(): void
    {
        $response = $this->get('/boom-runtime');

        $response->assertStatus(500);
        $response->assertDontSee('class="badge"', false);
    }
}
