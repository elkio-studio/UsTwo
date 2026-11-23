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

class WishesDrawerTest extends TestCase
{
    use RefreshDatabase;

    private const OTHER_DRAWERS = [
        'peliculas' => 'movie_slot', 'series' => 'series_slot',
        'sitios' => 'places_slot', 'lecturas' => 'reading_slot',
    ];

    public function test_members_can_place_move_and_open_wishes_without_affecting_other_shelves(): void
    {
        $this->withoutVite();
        $owner = $this->account('owner');
        $member = $this->account('member');
        $space = $this->shelf('Casa', $owner);
        $other = $this->shelf('Otra', $owner);
        $space->members()->attach($member);
        $base = '/espacios/'.$space->id;

        $this->get($base.'/deseos')->assertRedirect('/acceso');
        $this->put($base.'/cajones/deseos', ['slot' => 4])->assertRedirect('/acceso');
        $this->actingAs($member)->get('/espacios/'.$other->id.'/deseos')->assertNotFound();
        $this->put('/espacios/'.$other->id.'/cajones/deseos', ['slot' => 4])->assertNotFound();
        $this->assertNull($other->fresh()->wish_slot);

        $this->actingAs($owner)->get('/casa')->assertInertia(fn (Assert $page) => $page
            ->where('activeSpace.wish_slot', null));
        $this->put($base.'/cajones/deseos', ['slot' => 4])->assertRedirect('/casa');
        $this->actingAs($member)->put($base.'/cajones/deseos', ['slot' => 8])->assertRedirect('/casa');
        $this->get('/casa')->assertInertia(fn (Assert $page) => $page
            ->where('activeSpace.id', $space->id)->where('activeSpace.wish_slot', 8));
        $this->get($base.'/deseos')->assertInertia(fn (Assert $page) => $page
            ->component('Wishes/Wishes')->where('spaceId', $space->id)->where('spaceSlug', 'casa'));
        // El hueco anterior se libera y puede ocuparlo otro tipo.
        $this->put($base.'/cajones/peliculas', ['slot' => 4])->assertRedirect('/casa');
        $this->put($base.'/cajones/deseos', ['slot' => 8])->assertRedirect('/casa');
        $this->assertDatabaseHas('spaces', ['id' => $space->id, 'movie_slot' => 4, 'wish_slot' => 8]);
        $this->assertNull($other->fresh()->wish_slot);

        // El alta añadida en 031 sigue validando antes de guardar.
        $this->post($base.'/deseos', ['title' => 'No guardar'])->assertSessionHasErrors(['url', 'list_id']);
        $space->members()->detach($member);
        $this->get($base.'/deseos')->assertNotFound();
        $this->put($base.'/cajones/deseos', ['slot' => 2])->assertNotFound();
        $this->assertSame(8, $space->fresh()->wish_slot);
    }

    public function test_invalid_slots_and_collisions_preserve_the_entire_distribution(): void
    {
        $owner = $this->account('owner');
        $space = $this->shelf('Casa', $owner);
        $base = '/espacios/'.$space->id.'/cajones/';
        $this->actingAs($owner)->put($base.'deseos', ['slot' => 8])->assertRedirect('/casa');
        $distribution = $space->fresh()->getAttributes();

        foreach ([null, 0, 9, -1, 1.5, 'abc', [2]] as $invalid) {
            $this->put($base.'deseos', ['slot' => $invalid])->assertSessionHasErrors('slot');
            $this->assertSame($distribution, $space->fresh()->getAttributes());
        }

        foreach (self::OTHER_DRAWERS as $type => $column) {
            $this->put($base.$type, ['slot' => 8])->assertSessionHasErrors('slot');
            $this->assertSame($distribution, $space->fresh()->getAttributes());
            $this->put($base.$type, ['slot' => 2])->assertRedirect('/casa');
            $before = $space->fresh()->getAttributes();
            // El destino está ocupado al recibir la petición, aunque se eligiera antes.
            $this->put($base.'deseos', ['slot' => 2])->assertSessionHasErrors('slot');
            $this->assertSame($before, $space->fresh()->getAttributes());
            $this->put($base.$type, ['slot' => 3])->assertRedirect('/casa');
            Space::whereKey($space->id)->update([$column => null]);
            $distribution = $space->fresh()->getAttributes();
        }
    }

    public function test_database_rejects_wish_collisions_on_insert_and_updates_in_both_directions(): void
    {
        $owner = $this->account('owner');
        $space = $this->shelf('Casa', $owner);
        // Las posiciones restantes pueden ser null, sin desactivar la restricción.
        Space::whereKey($space->id)->update(['wish_slot' => 8]);

        foreach (self::OTHER_DRAWERS as $column) {
            $this->assertCollision(fn () => Space::whereKey($space->id)->update([$column => 8]));
            Space::whereKey($space->id)->update([$column => 2]);
            $this->assertCollision(fn () => Space::whereKey($space->id)->update(['wish_slot' => 2]));
            $this->assertCollision(fn () => DB::table('spaces')->insert([
                'name' => 'Inválida', 'slug' => 'invalida', 'owner_id' => $owner->id,
                'wish_slot' => 2, $column => 2,
            ]));
            $this->assertDatabaseHas('spaces', ['id' => $space->id, 'wish_slot' => 8, $column => 2]);
            Space::whereKey($space->id)->update([$column => null]);
        }
    }

    public function test_migration_preserves_existing_values_and_the_other_collision_guards(): void
    {
        $connection = DB::connection();
        $isolatedSqlite = $connection->getDriverName() === 'sqlite' && $connection->getDatabaseName() === ':memory:';
        $isolatedPostgres = $connection->getDriverName() === 'pgsql'
            && $connection->getDatabaseName() === 'ustwo_ci'
            && $connection->getConfig('host') === '127.0.0.1'
            && (int) $connection->getConfig('port') === 55432
            && $connection->getConfig('username') === 'ustwo_ci';
        $this->assertTrue(app()->environment('testing') && ($isolatedSqlite || $isolatedPostgres),
            'La prueba requiere SQLite en memoria o el PostgreSQL aislado del CI.');
        $owner = $this->account('owner');
        $space = $this->shelf('Casa', $owner);
        Space::whereKey($space->id)->update([
            'movie_slot' => 1, 'series_slot' => 2, 'places_slot' => 3, 'reading_slot' => 4,
        ]);
        $migration = require database_path('migrations/2026_11_23_000012_add_wish_slot_to_spaces.php');
        $migration->down();
        try {
            $this->assertFalse(Schema::hasColumn('spaces', 'wish_slot'));
            $before = [];
            foreach (Schema::getTableListing(schemaQualified: false) as $table) {
                if ($table === 'migrations') continue;
                $before[$table] = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
            }
        } finally {
            $migration->up();
        }
        foreach ($before as $table => $rows) {
            $after = DB::table($table)->get()->map(function ($row) use ($table) {
                $values = (array) $row;
                if ($table === 'spaces') {
                    $this->assertNull($values['wish_slot']);
                    unset($values['wish_slot']);
                }

                return $values;
            })->all();
            $this->assertSame($rows, $after, 'La migración cambió datos existentes de '.$table);
        }
        Space::whereKey($space->id)->update(['wish_slot' => 5]);
        $migration->down();
        try {
            $this->assertDatabaseHas('spaces', [
                'id' => $space->id, 'movie_slot' => 1, 'series_slot' => 2, 'places_slot' => 3, 'reading_slot' => 4,
            ]);
            try {
                DB::transaction(fn () => Space::whereKey($space->id)->update(['reading_slot' => 1]));
                $this->fail('Se perdió la restricción de Lecturas al revertir Deseos.');
            } catch (QueryException $exception) {
                $this->assertStringContainsString('spaces_distinct_reading_slot', $exception->getMessage());
            }
        } finally {
            $migration->up();
        }
    }

    private function assertCollision(callable $operation): void
    {
        try {
            DB::transaction($operation);
            $this->fail('Se permitió una colisión con Deseos.');
        } catch (QueryException $exception) {
            $this->assertStringContainsString('spaces_distinct_wish_slot', $exception->getMessage());
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
        $space->members()->attach($owner);

        return $space;
    }
}
