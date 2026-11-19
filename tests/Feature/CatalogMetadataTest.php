<?php

namespace Tests\Feature;

use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CatalogMetadataTest extends TestCase
{
    use RefreshDatabase;

    private Space $space;
    private User $owner;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Http::preventStrayRequests();
        $this->owner = User::create(['name' => 'Owner', 'username' => 'owner', 'username_key' => 'owner', 'email' => 'owner@example.test', 'password' => 'password']);
        $this->member = User::create(['name' => 'Member', 'username' => 'member', 'username_key' => 'member', 'email' => 'member@example.test', 'password' => 'password']);
        $this->space = Space::create(['name' => 'Casa', 'owner_id' => $this->owner->id]);
        $this->space->members()->attach([$this->owner->id, $this->member->id]);
    }

    public function test_imported_fields_are_locked_but_manual_completions_stay_editable_for_both_members(): void
    {
        foreach (['movies', 'series', 'books', 'manga', 'comics'] as $type) {
            $reading = in_array($type, ['books', 'manga', 'comics']);
            $relation = $reading ? 'readings' : $type;
            $entry = $this->space->{$relation}()->make($reading
                ? ['category' => $type, 'title' => 'Catálogo', 'title_key' => 'catalogo', 'provider' => 'open_library']
                : ['title' => 'Catálogo', 'tmdb_id' => 10, 'watched_on' => '2024-01-01']);
            $entry->forceFill(['catalog_fields' => ['title'], 'metadata_version' => 0])->save();
            $path = $reading ? 'lecturas' : ($type === 'movies' ? 'peliculas' : 'series');
            $url = '/espacios/'.$this->space->id.'/'.$path.'/'.$entry->id;
            $field = $reading ? 'description' : 'overview';
            $this->actingAs($this->owner)->from($url);
            $this->patch($url, [$field => 'Sin versión'])->assertSessionHasErrors('metadata_version');
            $this->patch($url, ['metadata_version' => 0, 'title' => 'Manipulado', $field => 'No guardar'])->assertSessionHasErrors('save');
            $this->assertNull($entry->fresh()->{$field});
            foreach (['catalog_fields' => [], 'provider' => 'manual', 'cover_url' => 'https://example.test', 'score' => 5] as $key => $value) {
                $this->patch($url, ['metadata_version' => 0, $key => $value])->assertSessionHasErrors('save');
            }
            $this->patch($url, ['metadata_version' => 0, $field => 'Primera'])->assertRedirect($url);
            $this->actingAs($this->member)->patch($url, ['metadata_version' => 1, $field => 'Corregida'])->assertRedirect($url);
            $this->assertSame('Corregida', $entry->fresh()->{$field});
            $this->assertSame(['title'], $entry->fresh()->catalog_fields);
            $this->patch($url, ['metadata_version' => 1, $field => 'Antigua'])->assertSessionHasErrors('save');
            $this->assertSame('Corregida', $entry->fresh()->{$field});
            $this->patch($url, ['metadata_version' => 2, $field => ''])->assertRedirect($url);
            $this->assertNull($entry->fresh()->{$field});
            $this->patch($url, ['metadata_version' => 3, $field => 'Otra vez'])->assertRedirect($url);
            $this->get($url)->assertInertia(fn (Assert $page) => $page
                ->where('editableMetadata', fn ($fields) => in_array($field, $fields->all()) && ! in_array('title', $fields->all()))
                ->where(($reading ? 'reading' : 'movie').'.metadata_version', 4));
        }
    }

    public function test_linking_uses_catalog_values_and_preserves_missing_manual_values_and_ratings(): void
    {
        config(['services.tmdb.token' => 'test']);
        foreach (['movies' => 'peliculas', 'series' => 'series'] as $table => $path) {
            $entry = $this->space->{$table}()->create(['title' => 'Ficha', 'release_year' => 2020, 'overview' => 'Manual', 'watched_on' => '2024-01-01']);
            foreach ([$this->owner, $this->member] as $person) {
                $entry->ratings()->create(['user_id' => $person->id, 'score' => 80, 'note' => 'Conservar']);
            }
            $base = '/espacios/'.$this->space->id.'/'.$path;
            Http::fake(['api.themoviedb.org/3/*' => Http::response(['id' => 20, 'title' => 'FICHA', 'name' => 'FICHA'])]);
            $this->actingAs($this->owner)->post($base, ['tmdb_id' => 20, 'candidate_id' => $entry->id])->assertRedirect($base.'/'.$entry->id);
            $entry->refresh();
            $this->assertSame('FICHA', $entry->title);
            $this->assertSame(2020, (int) $entry->release_year);
            $this->assertSame('Manual', $entry->overview);
            $this->assertSame(['title'], $entry->catalog_fields);
            $this->assertSame('2024-01-01', $entry->watched_on);
            $this->assertSame(2, $entry->ratings()->where('note', 'Conservar')->count());
            $this->patch($base.'/'.$entry->id, ['metadata_version' => 1, 'overview' => 'Corregida', 'release_year' => 2021])->assertRedirect();
        }
    }

    public function test_a_linking_request_does_not_overwrite_an_edit_made_during_the_catalog_call(): void
    {
        config(['services.tmdb.token' => 'test']);
        foreach (['movies' => 'peliculas', 'series' => 'series'] as $table => $path) {
            $entry = $this->space->{$table}()->create(['title' => 'Ficha', 'watched_on' => '2024-01-01']);
            Http::fake(function () use ($entry) {
                $entry->forceFill(['overview' => 'Edición simultánea', 'metadata_version' => 1])->save();

                return Http::response(['id' => 30, 'title' => 'Ficha', 'name' => 'Ficha', 'overview' => 'Externa']);
            });
            $this->actingAs($this->owner)->post('/espacios/'.$this->space->id.'/'.$path, ['tmdb_id' => 30, 'candidate_id' => $entry->id])
                ->assertSessionHasErrors('match');
            $this->assertNull($entry->fresh()->tmdb_id);
            $this->assertSame('Edición simultánea', $entry->fresh()->overview);
        }
    }

    public function test_catalog_creation_protects_all_returned_fields_and_versions_only_real_changes(): void
    {
        config(['services.tmdb.token' => 'test']);
        Http::fake(['api.themoviedb.org/3/*' => Http::response([
            'id' => 40, 'title' => 'Externa', 'name' => 'Externa', 'release_date' => '2020-01-01',
            'first_air_date' => '2020-01-01', 'overview' => 'Sinopsis externa', 'poster_path' => '/cover.jpg',
        ])]);
        foreach (['movies' => 'peliculas', 'series' => 'series'] as $table => $path) {
            $base = '/espacios/'.$this->space->id.'/'.$path;
            $this->actingAs($this->owner)->post($base, ['tmdb_id' => 40])->assertRedirect();
            $entry = $this->space->{$table}()->sole();
            $this->assertSame(['title', 'release_year', 'overview', 'poster_path'], $entry->catalog_fields);
            foreach (['title' => 'Cambio', 'release_year' => 2021, 'overview' => 'Cambio', 'poster_path' => null] as $field => $value) {
                $this->patch($base.'/'.$entry->id, ['metadata_version' => 0, $field => $value, 'watched_on' => '2024-01-01'])
                    ->assertSessionHasErrors('save');
                $this->assertSame(0, $entry->fresh()->metadata_version);
            }
            $this->patch($base.'/'.$entry->id, ['metadata_version' => 0, 'watched_on' => $entry->watched_on])->assertRedirect();
            $this->assertSame(0, $entry->fresh()->metadata_version);
        }
    }

    public function test_an_edit_cannot_overwrite_a_field_linked_after_its_permission_check(): void
    {
        $entry = $this->space->movies()->create(['title' => 'Ficha', 'watched_on' => '2024-01-01']);
        $interleaved = false;
        DB::connection()->beforeExecuting(function ($sql) use (&$interleaved, $entry) {
            if (! $interleaved && str_starts_with($sql, 'update "movies"')) {
                $interleaved = true;
                $entry->forceFill(['tmdb_id' => 10, 'overview' => 'Externa', 'catalog_fields' => ['title', 'overview'], 'metadata_version' => 1])->save();
            }
        });
        $this->actingAs($this->owner)->patch('/espacios/'.$this->space->id.'/peliculas/'.$entry->id, ['metadata_version' => 0, 'overview' => 'Manual'])
            ->assertSessionHasErrors('save');
        $this->assertTrue($interleaved);
        $this->assertSame('Externa', $entry->fresh()->overview);
        $this->assertSame(['title', 'overview'], $entry->fresh()->catalog_fields);
    }

    public function test_catalog_creation_and_linking_failures_preserve_values_provenance_ratings_and_input(): void
    {
        config(['services.tmdb.token' => 'test']);
        Http::fake(['api.themoviedb.org/3/*' => Http::response([
            'id' => 60, 'title' => 'Ficha', 'name' => 'Ficha', 'overview' => 'Importada',
        ])]);
        foreach (['movies' => 'peliculas', 'series' => 'series'] as $table => $path) {
            $base = '/espacios/'.$this->space->id.'/'.$path;
            $failure = (object) ['operation' => 'insert'];
            DB::connection()->beforeExecuting(function ($sql) use ($table, $failure) {
                $prefix = $failure->operation === 'update' ? 'update "'.$table.'"' : 'insert into "'.$table.'"';
                if ($failure->operation !== null && str_starts_with($sql, $prefix)) {
                    throw new \Illuminate\Database\QueryException('sqlite', $sql, [], new \RuntimeException('Fallo simulado'));
                }
            });
            try {
                $this->actingAs($this->owner)->from($base.'/nueva')->post($base, ['tmdb_id' => 60])
                    ->assertRedirect($base.'/nueva')->assertSessionHasErrors('save')->assertSessionHasInput('tmdb_id', 60);
                $this->assertSame(0, $this->space->{$table}()->count());
                $failure->operation = null;
                $entry = $this->space->{$table}()->make(['title' => 'Ficha', 'overview' => 'Manual', 'watched_on' => '2024-01-01']);
                $entry->forceFill(['catalog_fields' => [], 'metadata_version' => 0])->save();
                $entry->ratings()->create(['user_id' => $this->owner->id, 'score' => 75, 'note' => 'Conservar']);
                $failure->operation = 'update';
                $this->post($base, ['tmdb_id' => 60, 'candidate_id' => $entry->id])
                    ->assertRedirect($base.'/nueva')->assertSessionHasErrors('save')->assertSessionHasInput('candidate_id', $entry->id);
                $this->assertNull($entry->fresh()->tmdb_id);
                $this->assertSame('Manual', $entry->fresh()->overview);
                $this->assertSame([], $entry->fresh()->catalog_fields);
                $this->assertSame(0, $entry->fresh()->metadata_version);
                $this->assertSame('Conservar', $entry->ratings()->sole()->note);
            } finally {
                $failure->operation = null;
            }
        }
    }

    public function test_migration_classifies_existing_records_without_changing_values_or_ratings(): void
    {
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        $migration = require database_path('migrations/2026_11_19_000011_add_catalog_metadata_provenance.php');
        $migration->down();
        $snapshots = [];
        foreach (['movies', 'series', 'readings'] as $table) {
            $common = ['space_id' => $this->space->id, 'title' => 'Existente', 'created_at' => '2020-01-01 00:00:00', 'updated_at' => '2020-01-01 00:00:00'];
            $values = $table === 'readings'
                ? ['category' => 'books', 'title_key' => 'existente', 'provider' => 'open_library', 'description' => '   ']
                : ['tmdb_id' => 100, 'release_year' => 2020, 'watched_on' => '2024-01-01'];
            $id = DB::table($table)->insertGetId($common + $values);
            $manualId = DB::table($table)->insertGetId($common + ($table === 'readings'
                ? ['category' => 'books', 'title_key' => 'manual'] : ['watched_on' => '2024-01-01']));
            $snapshots[$table] = DB::table($table)->orderBy('id')->get()->map(fn ($row) => (array) $row)->all();
            $ratingTable = $table === 'movies' ? 'movie_ratings' : ($table === 'series' ? 'series_ratings' : 'reading_ratings');
            $foreignKey = $table === 'movies' ? 'movie_id' : ($table === 'series' ? 'series_id' : 'reading_id');
            DB::table($ratingTable)->insert([$foreignKey => $id, 'user_id' => $this->owner->id, 'score' => 50, 'note' => 'Conservar']);
        }
        $migration->up();
        foreach ($snapshots as $table => $rows) {
            foreach ($rows as $row) {
                $actual = (array) DB::table($table)->find($row['id']);
                $fields = json_decode($actual['catalog_fields'], true, flags: JSON_THROW_ON_ERROR);
                $this->assertSame(0, $actual['metadata_version']);
                unset($actual['catalog_fields'], $actual['metadata_version']);
                $this->assertSame($row, $actual);
                $this->assertSame(($row[$table === 'readings' ? 'provider' : 'tmdb_id'] ?? null) === null ? [] : ($table === 'readings' ? ['title'] : ['title', 'release_year']), $fields);
            }
        }
        foreach (['movie_ratings', 'series_ratings', 'reading_ratings'] as $table) {
            $this->assertDatabaseHas($table, ['user_id' => $this->owner->id, 'score' => 50, 'note' => 'Conservar']);
        }
        $reading = $this->space->readings()->whereNotNull('provider')->firstOrFail();
        $this->actingAs($this->owner)->patch('/espacios/'.$this->space->id.'/lecturas/'.$reading->id, [
            'metadata_version' => 0, 'description' => 'Completada a mano',
        ])->assertRedirect();
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->assertSame(['title'], $reading->fresh()->catalog_fields);
        $this->assertContains('description', $reading->fresh()->editableMetadata());
    }
}
