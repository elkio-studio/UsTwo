<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EntryPointsTest extends TestCase
{
    public function test_login_page_contains_the_expected_inertia_component(): void
    {
        $this->withoutVite();

        $this->get('/acceso')
            ->assertOk()
            ->assertSee('lang="es"', false)
            ->assertInertia(fn (Assert $page) => $page->component('Login/Login'));

        $this->assertSame([], DB::getConnections());
    }

    public function test_inertia_requests_receive_json_without_opening_database_connections(): void
    {
        $this->withoutVite();
        $initialPage = $this->get('/acceso')->assertOk()->viewData('page');

        $this->get('/acceso', [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
            'X-Inertia-Version' => $initialPage['version'] ?? '',
        ])
            ->assertOk()
            ->assertHeader('X-Inertia', 'true')
            ->assertJsonPath('component', 'Login/Login')
            ->assertJsonPath('url', '/acceso');

        $this->assertSame([], DB::getConnections());
    }

    public function test_technical_health_route_works_without_a_database(): void
    {
        $this->get('/up')->assertOk();

        $this->assertSame([], DB::getConnections());
        $this->assertSame('sync', config('queue.default'));
    }
}
