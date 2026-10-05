<?php

namespace Datalogix\ErrorPages\Tests\Feature;

use Datalogix\ErrorPages\Tests\TestCase;
use Illuminate\Support\Facades\App;

class FallbackRenderingTest extends TestCase
{
    public function test_fallback_view_renders_for_an_http_exception(): void
    {
        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        $response = $this->get('/boom/403');

        $response->assertStatus(403);
        $response->assertSee('class="badge"', false);
        $response->assertSee($messages[403]['title']);
        $response->assertSee($messages[403]['description']);
        $response->assertSee($messages['back_home']);
    }

    public function test_fallback_view_uses_locale_specific_copy(): void
    {
        $en = require dirname(__DIR__, 2).'/lang/en/messages.php';
        $ptBr = require dirname(__DIR__, 2).'/lang/pt_BR/messages.php';

        App::setLocale('pt_BR');

        $response = $this->get('/boom/403');

        $response->assertStatus(403);
        $response->assertSee($ptBr[403]['title']);
        $response->assertSee($ptBr[403]['description']);
        $response->assertDontSee($en[403]['title']);
    }

    public function test_untranslated_locale_falls_back_to_english_copy(): void
    {
        $en = require dirname(__DIR__, 2).'/lang/en/messages.php';

        App::setLocale('es');
        app('translator')->setFallback('es');

        $response = $this->get('/boom/404');

        $response->assertStatus(404);
        $response->assertSee('<html lang="en">', false);
        $response->assertSee($en[404]['title']);
        $response->assertSee($en['back_home']);
    }

    public function test_original_exception_headers_are_preserved(): void
    {
        $response = $this->get('/boom-headers');

        $response->assertStatus(419);
        $response->assertHeader('X-Test', 'header-value');
    }

    public function test_router_allow_header_is_preserved_via_method_not_allowed(): void
    {
        $response = $this->post('/only-get');

        $response->assertStatus(405);
        $response->assertHeader('Allow');
        $this->assertStringContainsString('GET', $response->headers->get('Allow'));
    }
}
