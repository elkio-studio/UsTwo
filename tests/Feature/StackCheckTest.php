<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StackCheckTest extends TestCase
{
    public function test_initial_page_contains_the_expected_inertia_component_and_props(): void
    {
        $this->withoutVite();

        $this->get('/')
            ->assertOk()
            ->assertSee('lang="es"', false)
            ->assertInertia(fn (Assert $page) => $page
                ->component('StackCheck')
                ->where('message', 'Laravel e Inertia están conectados con React.')
            );

        $this->assertNull(config('database.default'));
        $this->assertSame([], DB::getConnections());
    }

    public function test_inertia_requests_receive_json_without_opening_database_connections(): void
    {
        $this->withoutVite();
        $initialPage = $this->get('/')->assertOk()->viewData('page');

        $this->get('/', [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
            'X-Inertia-Version' => $initialPage['version'] ?? '',
        ])
            ->assertOk()
            ->assertHeader('X-Inertia', 'true')
            ->assertJsonPath('component', 'StackCheck')
            ->assertJsonPath('props.message', 'Laravel e Inertia están conectados con React.')
            ->assertJsonPath('url', '/');

        $this->assertSame([], DB::getConnections());
    }

    public function test_technical_health_route_works_without_a_database(): void
    {
        $this->get('/up')->assertOk();

        $this->assertSame([], DB::getConnections());
        $this->assertSame('sync', config('queue.default'));
        $this->assertFileDoesNotExist(database_path('database.sqlite'));
    }
}
