<?php

namespace Datalogix\ErrorPages\Tests\Feature;

use Datalogix\ErrorPages\Tests\TestCase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\View;
use Illuminate\View\ViewException;
use TALLKit\TALLKitServiceProvider;

require_once __DIR__.'/../Fixtures/TALLKitServiceProviderStub.php';

class TallkitRenderingTest extends TestCase
{
    public function test_tallkit_view_is_selected_and_rendered_when_tallkit_provider_is_loaded(): void
    {
        $this->app->register(TALLKitServiceProvider::class);
        View::prependNamespace('error-pages', __DIR__.'/../Fixtures/views');

        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        $response = $this->get('/boom/404');

        $response->assertStatus(404);
        $response->assertSee('tallkit-fixture', false);
        $response->assertSee($messages[404]['title']);
        $response->assertDontSee('class="badge"', false);
    }

    public function test_fallback_view_is_used_when_tallkit_is_installed_but_provider_not_loaded(): void
    {
        View::prependNamespace('error-pages', __DIR__.'/../Fixtures/views');

        $response = $this->get('/boom/404');

        $response->assertStatus(404);
        $response->assertSee('class="badge"', false);
        $response->assertDontSee('tallkit-fixture', false);
    }

    public function test_fallback_view_is_used_and_failure_reported_when_tallkit_view_throws(): void
    {
        Exceptions::fake();

        $this->app->register(TALLKitServiceProvider::class);
        View::prependNamespace('error-pages', __DIR__.'/../Fixtures/broken-views');

        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        $response = $this->get('/boom/500');

        $response->assertStatus(500);
        $response->assertSee('class="badge"', false);
        $response->assertSee($messages[500]['title']);

        Exceptions::assertReported(fn (ViewException $e) => str_contains($e->getMessage(), 'TALLKit layout failed to render'));
    }
}
