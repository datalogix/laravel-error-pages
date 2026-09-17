<?php

namespace Datalogix\ErrorPages\Tests\Feature;

use Datalogix\ErrorPages\Tests\TestCase;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class TallkitRenderingTest extends TestCase
{
    #[RunInSeparateProcess]
    public function test_tallkit_view_is_selected_and_rendered_when_tallkit_package_is_present(): void
    {
        require_once __DIR__.'/../Fixtures/TALLKitServiceProviderStub.php';

        View::prependNamespace('error-pages', __DIR__.'/../Fixtures/views');

        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        $response = $this->get('/boom/404');

        $response->assertStatus(404);
        $response->assertSee('tallkit-fixture', false);
        $response->assertSee($messages['default']['title']);
        $response->assertDontSee('class="badge"', false);
    }
}
