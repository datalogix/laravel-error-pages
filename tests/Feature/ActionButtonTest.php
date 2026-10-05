<?php

namespace Datalogix\ErrorPages\Tests\Feature;

use Datalogix\ErrorPages\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class ActionButtonTest extends TestCase
{
    public function test_default_action_links_to_home(): void
    {
        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        $response = $this->get('/boom/404');

        $response->assertSee('href="'.url('/').'"', false);
        $response->assertSee($messages['back_home']);
    }

    public function test_maintenance_page_has_no_action(): void
    {
        $response = $this->get('/boom/503');

        $response->assertStatus(503);
        $response->assertSee('class="badge"', false);
        $response->assertDontSee('class="button"', false);
    }

    public function test_unauthorized_links_to_login_when_route_exists(): void
    {
        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        Route::get('/login', fn () => 'login')->name('login');
        Route::getRoutes()->refreshNameLookups();

        $response = $this->get('/boom/401');

        $response->assertSee('href="'.url('/login').'"', false);
        $response->assertSee($messages['sign_in']);
    }

    public function test_unauthorized_links_to_home_without_login_route(): void
    {
        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        $response = $this->get('/boom/401');

        $response->assertSee('href="'.url('/').'"', false);
        $response->assertSee($messages['back_home']);
    }

    public function test_expired_session_links_back_to_previous_page(): void
    {
        $messages = require dirname(__DIR__, 2).'/lang/en/messages.php';

        $response = $this->from('/checkout')->get('/boom/419');

        $response->assertSee('href="'.url('/checkout').'"', false);
        $response->assertSee($messages['go_back']);
    }
}
