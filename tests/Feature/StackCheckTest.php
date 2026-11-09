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

        $this->get('/comprobacion')
            ->assertOk()
            ->assertSee('lang="es"', false)
            ->assertInertia(fn (Assert $page) => $page
                ->component('StackCheck/StackCheck')
                ->where('message', 'Laravel e Inertia están conectados con React.')
            );

        $this->assertSame([], DB::getConnections());
    }

    public function test_inertia_requests_receive_json_without_opening_database_connections(): void
    {
        $this->withoutVite();
        $initialPage = $this->get('/comprobacion')->assertOk()->viewData('page');

        $this->get('/comprobacion', [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
            'X-Inertia-Version' => $initialPage['version'] ?? '',
        ])
            ->assertOk()
            ->assertHeader('X-Inertia', 'true')
            ->assertJsonPath('component', 'StackCheck/StackCheck')
            ->assertJsonPath('props.message', 'Laravel e Inertia están conectados con React.')
            ->assertJsonPath('url', '/comprobacion');

        $this->assertSame([], DB::getConnections());
    }

    public function test_technical_health_route_works_without_a_database(): void
    {
        $this->get('/up')->assertOk();

        $this->assertSame([], DB::getConnections());
        $this->assertSame('sync', config('queue.default'));
    }
}
