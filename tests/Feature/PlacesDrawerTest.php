<?php

namespace Tests\Feature;

use App\Models\Space;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PlacesDrawerTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_can_place_move_and_open_places_in_their_shelf(): void
    {
        $this->withoutVite();
        $owner = $this->account('owner');
        $member = $this->account('member');
        $space = $this->shelf('Casa', $owner);
        $other = $this->shelf('Otra', $owner);
        $space->members()->attach($member->id);
        $base = '/espacios/'.$space->id;

        $this->get($base.'/sitios')->assertRedirect('/acceso');
        $this->put($base.'/cajones/sitios', ['slot' => 4])->assertRedirect('/acceso');
        $this->actingAs($member)->get('/espacios/'.$other->id.'/sitios')->assertNotFound();
        $this->put('/espacios/'.$other->id.'/cajones/sitios', ['slot' => 4])->assertNotFound();

        $this->actingAs($owner)->put($base.'/cajones/sitios', ['slot' => 4])->assertRedirect('/casa');
        $this->actingAs($member)->put($base.'/cajones/sitios', ['slot' => 8])->assertRedirect('/casa');
        $this->get('/casa')->assertInertia(fn (Assert $page) => $page->where('activeSpace.places_slot', 8));
        $this->get($base.'/sitios')->assertInertia(fn (Assert $page) => $page
            ->component('Places/Places')->where('spaceSlug', 'casa'));
        $this->put($base.'/cajones/peliculas', ['slot' => 4])->assertRedirect('/casa');
        $this->assertDatabaseHas('spaces', ['id' => $space->id, 'movie_slot' => 4, 'places_slot' => 8]);
        $this->assertDatabaseHas('spaces', ['id' => $other->id, 'places_slot' => null]);
    }

    public function test_places_rejects_invalid_and_occupied_slots_and_database_prevents_collisions(): void
    {
        $owner = $this->account('owner');
        $space = $this->shelf('Casa', $owner);
        $base = '/espacios/'.$space->id.'/cajones/';
        $this->actingAs($owner);
        $this->put($base.'sitios', ['slot' => 0])->assertSessionHasErrors('slot');
        $this->put($base.'sitios', ['slot' => 9])->assertSessionHasErrors('slot');
        $this->put($base.'peliculas', ['slot' => 2])->assertRedirect('/casa');
        $this->put($base.'series', ['slot' => 3])->assertRedirect('/casa');
        $this->put($base.'sitios', ['slot' => 4])->assertRedirect('/casa');
        $this->put($base.'sitios', ['slot' => 2])->assertSessionHasErrors('slot');
        $this->put($base.'sitios', ['slot' => 3])->assertSessionHasErrors('slot');

        foreach (['peliculas' => 'movie_slot', 'series' => 'series_slot'] as $drawer => $column) {
            $this->put($base.$drawer, ['slot' => 4])->assertSessionHasErrors('slot');
            try {
                DB::transaction(fn () => Space::whereKey($space->id)->update([$column => 4]));
                $this->fail('La base de datos permitió una colisión con Sitios.');
            } catch (QueryException $exception) {
                $this->assertStringContainsString('spaces_distinct_places_slot', $exception->getMessage());
            }
        }

        $this->assertDatabaseHas('spaces', ['id' => $space->id, 'movie_slot' => 2, 'series_slot' => 3, 'places_slot' => 4]);
    }

    public function test_rolling_back_places_keeps_the_previous_drawers_and_their_collision_protection(): void
    {
        $space = $this->shelf('Casa', $this->account('owner'));
        Space::whereKey($space->id)->update(['movie_slot' => 2, 'series_slot' => 3, 'places_slot' => 4]);
        $migration = require database_path('migrations/2026_11_13_000006_add_places_slot_to_spaces.php');
        $readings = require database_path('migrations/2026_11_16_000008_add_reading_slot_to_spaces.php');
        $readings->down();

        try {
            $migration->down();
            $this->assertFalse(Schema::hasColumn('spaces', 'places_slot'));
            $this->assertDatabaseHas('spaces', ['id' => $space->id, 'movie_slot' => 2, 'series_slot' => 3]);

            try {
                DB::transaction(fn () => Space::whereKey($space->id)->update(['movie_slot' => 3]));
                $this->fail('La reversión eliminó la protección de Películas y Series.');
            } catch (QueryException $exception) {
                $this->assertStringContainsString('spaces_distinct_slots', $exception->getMessage());
            }
        } finally {
            $migration->up();
            $readings->up();
        }
    }

    public function test_members_can_place_move_and_open_readings_in_their_shelf(): void
    {
        $this->withoutVite();
        $owner = $this->account('owner');
        $member = $this->account('member');
        $space = $this->shelf('Casa', $owner);
        $other = $this->shelf('Otra', $owner);
        $space->members()->attach($member);
        $base = '/espacios/'.$space->id;
        $otherBase = '/espacios/'.$other->id;

        $this->get($base.'/lecturas')->assertRedirect('/acceso');
        $this->put($base.'/cajones/lecturas', ['slot' => 4])->assertRedirect('/acceso');
        $this->actingAs($member)->get($otherBase.'/lecturas')->assertNotFound();
        $this->put($otherBase.'/cajones/lecturas', ['slot' => 4])->assertNotFound();
        $this->actingAs($owner)->put($base.'/cajones/lecturas', ['slot' => 4])->assertRedirect('/casa');
        $this->actingAs($member)->put($base.'/cajones/lecturas', ['slot' => 8])->assertRedirect('/casa');
        $this->get('/casa')->assertInertia(fn (Assert $page) => $page->where('activeSpace.reading_slot', 8));
        $this->get($base.'/lecturas')->assertInertia(fn (Assert $page) => $page
            ->component('Readings/Readings')->where('spaceSlug', 'casa'));
        $this->put($base.'/cajones/sitios', ['slot' => 4])->assertRedirect('/casa');
        $this->assertDatabaseHas('spaces', ['id' => $space->id, 'reading_slot' => 8, 'places_slot' => 4]);
        $this->assertDatabaseHas('spaces', ['id' => $other->id, 'reading_slot' => null]);
    }

    public function test_readings_rejects_collisions_in_both_directions(): void
    {
        $owner = $this->account('owner');
        $space = $this->shelf('Casa', $owner);
        $base = '/espacios/'.$space->id.'/cajones/';
        $this->actingAs($owner);
        $this->put($base.'lecturas', ['slot' => 0])->assertSessionHasErrors('slot');
        $this->put($base.'lecturas', ['slot' => 9])->assertSessionHasErrors('slot');
        $this->put($base.'lecturas', ['slot' => 8])->assertRedirect('/casa');

        foreach (['peliculas' => 'movie_slot', 'series' => 'series_slot', 'sitios' => 'places_slot'] as $drawer => $column) {
            $this->put($base.$drawer, ['slot' => 8])->assertSessionHasErrors('slot');
            $this->put($base.$drawer, ['slot' => 2])->assertRedirect('/casa');
            $this->put($base.'lecturas', ['slot' => 2])->assertSessionHasErrors('slot');
            try {
                DB::transaction(fn () => Space::whereKey($space->id)->update([$column => 8]));
                $this->fail('La base de datos permitió una colisión con Lecturas.');
            } catch (QueryException $exception) {
                $this->assertStringContainsString('spaces_distinct_reading_slot', $exception->getMessage());
            }
            Space::whereKey($space->id)->update([$column => null]);
        }
    }

    public function test_rolling_back_readings_keeps_the_other_drawers(): void
    {
        $space = $this->shelf('Casa', $this->account('owner'));
        $slots = ['movie_slot' => 2, 'series_slot' => 3, 'places_slot' => 4];
        Space::whereKey($space->id)->update($slots + ['reading_slot' => 8]);
        $migration = require database_path('migrations/2026_11_16_000008_add_reading_slot_to_spaces.php');

        $migration->down();
        try {
            $this->assertFalse(Schema::hasColumn('spaces', 'reading_slot'));
            $this->assertDatabaseHas('spaces', ['id' => $space->id] + $slots);
        } finally {
            $migration->up();
        }
    }

    private function account(string $username): User
    {
        return User::create([
            'name' => $username, 'username' => $username, 'username_key' => $username,
            'email' => $username.'@example.test', 'password' => 'test-password',
        ]);
    }

    private function shelf(string $name, User $owner): Space
    {
        $space = Space::create(['name' => $name, 'owner_id' => $owner->id]);
        $space->members()->attach($owner->id);

        return $space;
    }
}
