<?php

namespace Datalogix\ErrorPages\Tests\Feature;

use Datalogix\ErrorPages\Tests\TestCase;

class ConfigGatingTest extends TestCase
{
    public function test_disabled_config_lets_exception_pass_through(): void
    {
        config()->set('error-pages.enabled', false);

        $response = $this->get('/boom/404');

        $response->assertStatus(404);
        $response->assertDontSee('class="badge"', false);
    }

    public function test_codes_restriction_only_intercepts_listed_codes(): void
    {
        config()->set('error-pages.codes', [403]);

        $this->get('/boom/403')->assertStatus(403)->assertSee('class="badge"', false);
        $this->get('/boom/404')->assertStatus(404)->assertDontSee('class="badge"', false);
    }

    public function test_config_changes_at_runtime_are_respected(): void
    {
        $this->get('/boom/404')->assertSee('class="badge"', false);

        config()->set('error-pages.enabled', false);

        $this->get('/boom/404')->assertDontSee('class="badge"', false);
    }

    public function test_exception_message_is_hidden_by_default(): void
    {
        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        $response = $this->get('/boom-message');

        $response->assertStatus(403);
        $response->assertSee($messages[403]['description']);
        $response->assertDontSee('Upgrade your plan to access this');
    }

    public function test_exception_message_replaces_description_when_enabled(): void
    {
        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        config()->set('error-pages.show_exception_message', true);

        $response = $this->get('/boom-message');

        $response->assertStatus(403);
        $response->assertSee($messages[403]['title']);
        $response->assertSee('Upgrade your plan to access this');
        $response->assertDontSee($messages[403]['description']);
    }

    public function test_empty_exception_message_keeps_translated_description(): void
    {
        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        config()->set('error-pages.show_exception_message', true);

        $this->get('/boom/403')->assertSee($messages[403]['description']);
    }

    public function test_application_error_view_takes_precedence(): void
    {
        $response = $this->get('/boom/418');

        $response->assertStatus(418);
        $response->assertSee('App-defined 418 page');
        $response->assertDontSee('class="badge"', false);

        $this->get('/boom/404')->assertSee('class="badge"', false);
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
