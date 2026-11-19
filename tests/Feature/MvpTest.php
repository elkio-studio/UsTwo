<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MvpTest extends TestCase
{
    use RefreshDatabase;

    private User $angel;
    private User $alejandra;
    private Space $space;
    private string $moviesUrl;
    private string $candidateKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->angel = User::create(['name' => 'Ángel', 'email' => 'angel@example.test', 'password' => 'example-password', 'username' => 'angel', 'username_key' => 'angel']);
        $this->alejandra = User::create(['name' => 'Alejandra', 'email' => 'alejandra@example.test', 'password' => 'another-example-password', 'username' => 'alejandra', 'username_key' => 'alejandra']);
        $this->space = Space::create(['name' => 'Nuestra estantería', 'owner_id' => $this->angel->id]);
        $this->space->members()->attach([$this->angel->id, $this->alejandra->id]);
        $this->moviesUrl = route('movies.index', $this->space, false);
        $this->candidateKey = 'movie_candidates.'.$this->space->id;
        Http::preventStrayRequests();
    }

    public function test_private_pages_and_actions_require_an_individual_session(): void
    {
        $this->get('/')->assertRedirect('/acceso');
        $this->get($this->moviesUrl)->assertRedirect('/acceso');
        $this->getJson($this->moviesUrl.'/catalogo?q=Alien')->assertUnauthorized();
        $this->post($this->moviesUrl)->assertRedirect('/acceso');
        $this->post('/acceso', ['email' => $this->angel->email, 'password' => 'incorrect'])->assertSessionHasErrors('email');
        $this->post('/acceso', ['email' => $this->angel->email, 'password' => 'example-password'])->assertRedirect();
        $this->assertAuthenticatedAs($this->angel);
        $this->get('/')->assertInertia(fn (Assert $page) => $page->component('Dashboard/Dashboard'));
        $this->post('/salir')->assertRedirect('/acceso');
        $this->get($this->moviesUrl)->assertRedirect('/acceso');
        $this->post('/acceso', ['email' => $this->alejandra->email, 'password' => 'another-example-password'])->assertRedirect();
        $this->assertAuthenticatedAs($this->alejandra);
    }

    public function test_manual_movies_persist_and_are_shared_and_ordered(): void
    {
        $this->actingAs($this->angel)->get($this->moviesUrl)->assertInertia(fn (Assert $page) => $page->has('movies', 0));
        $this->get($this->moviesUrl.'/nueva')->assertInertia(fn (Assert $page) => $page->component('MovieCreate/MovieCreate'));
        $this->post($this->moviesUrl, ['title' => 'Sin fecha'])->assertSessionHasErrors('watched_on');
        $this->post($this->moviesUrl, ['watched_on' => '2024-01-01'])->assertSessionHasErrors('title');
        $this->post($this->moviesUrl, ['title' => 'Alien', 'watched_on' => '2024-01-01'])
            ->assertRedirect(route('movies.show', [$this->space, Movie::where('title', 'Alien')->firstOrFail()]));
        $this->actingAs($this->alejandra)->post($this->moviesUrl, ['title' => 'Arrival', 'watched_on' => '2025-01-01'])
            ->assertRedirect(route('movies.show', [$this->space, Movie::where('title', 'Arrival')->firstOrFail()]));
        $this->get($this->moviesUrl)->assertInertia(fn (Assert $page) => $page
            ->where('movies.0.title', 'Arrival')->where('movies.1.title', 'Alien')->missing('movies.1.watched_on'));
        $alien = route('movies.show', [$this->space, Movie::where('title', 'Alien')->firstOrFail()]);
        $this->patchMetadata($alien, ['watched_on' => '2025-01-02'])->assertRedirect($alien);
        $this->actingAs($this->angel)->get($alien)->assertInertia(fn (Assert $page) => $page
            ->where('movie.watched_on', '2025-01-02'));
        $this->assertDatabaseCount('movies', 2);
    }

    public function test_manual_movie_and_series_metadata_is_validated_scoped_and_preserves_ratings(): void
    {
        foreach (['movies', 'series'] as $collection) {
            $entry = $this->space->{$collection}()->create(['title' => 'Original', 'watched_on' => '2024-01-01']);
            $entry->ratings()->create(['user_id' => $this->angel->id, 'score' => 80, 'note' => 'Conservar']);
            $url = '/espacios/'.$this->space->id.'/'.($collection === 'movies' ? 'peliculas' : 'series').'/'.$entry->id;
            $this->actingAs($this->alejandra)->from($url);
            $this->patchMetadata($url, ['title' => 'Corregida', 'overview' => str_repeat('a', 20000)])
                ->assertRedirect($url);
            $this->assertSame('Corregida', $entry->fresh()->title);
            $this->assertSame(20000, strlen($entry->fresh()->overview));
            $this->assertSame('2024-01-01', $entry->fresh()->watched_on);
            foreach ([['title' => ' '], ['overview' => str_repeat('a', 20001)], ['release_year' => 999], ['watched_on' => 'invalid']] as $invalid) {
                $this->patchMetadata($url, $invalid + ['overview' => 'No guardar'])->assertSessionHasErrors(array_key_first($invalid));
                $this->assertSame(20000, strlen($entry->fresh()->overview));
            }
            $this->patchMetadata($url, ['overview' => '', 'release_year' => null])->assertRedirect($url);
            $this->assertNull($entry->fresh()->overview);
            $this->assertNull($entry->fresh()->tmdb_id);
            $this->assertSame('Conservar', $entry->ratings()->sole()->note);

            $other = Space::create(['name' => 'Otra '.$collection, 'owner_id' => $this->angel->id]);
            $other->members()->attach($this->angel->id);
            $this->patchMetadata('/espacios/'.$other->id.'/'.($collection === 'movies' ? 'peliculas' : 'series').'/'.$entry->id, ['title' => 'Ajena'])->assertNotFound();
            $other->members()->attach($this->alejandra->id);
            $this->patchMetadata('/espacios/'.$other->id.'/'.($collection === 'movies' ? 'peliculas' : 'series').'/'.$entry->id, ['title' => 'Ajena'])->assertNotFound();
            $this->assertSame('Corregida', $entry->fresh()->title);

            $entry->forceFill(['tmdb_id' => 999, 'overview' => 'Importada', 'catalog_fields' => ['title', 'overview']])->save();
            $this->patchMetadata($url, ['title' => 'No guardar', 'overview' => 'Manual'])->assertSessionHasErrors('save');
            $this->assertSame('Corregida', $entry->fresh()->title);
            $this->assertSame('Importada', $entry->fresh()->overview);
        }
    }

    public function test_metadata_storage_failure_preserves_the_record_and_submitted_input(): void
    {
        foreach (['movies', 'series'] as $collection) {
            $entry = $this->space->{$collection}()->create(['title' => 'Original', 'watched_on' => '2024-01-01']);
            $url = '/espacios/'.$this->space->id.'/'.($collection === 'movies' ? 'peliculas' : 'series').'/'.$entry->id;
            $connection = DB::connection();
            $fail = true;
            $connection->beforeExecuting(function ($sql) use ($collection, &$fail) {
                if ($fail && str_starts_with($sql, 'update "'.$collection.'"')) {
                    throw new QueryException('sqlite', $sql, [], new \Exception('Fallo simulado'));
                }
            });
            try {
                $this->actingAs($this->angel)->from($url)->patchMetadata($url, ['title' => 'Intento', 'overview' => 'Conservar en formulario'])
                    ->assertRedirect($url)->assertSessionHasErrors('save')->assertSessionHasInput('overview', 'Conservar en formulario');
                $this->assertSame('Original', $entry->fresh()->title);
                $this->assertNull($entry->fresh()->overview);
            } finally {
                $fail = false;
            }
        }
    }

    public function test_movie_and_series_ratings_stay_with_their_author_through_metadata_changes_and_linking(): void
    {
        config(['services.tmdb.token' => 'test-token']);
        foreach (['movies' => 'peliculas', 'series' => 'series'] as $collection => $path) {
            $entry = $this->space->{$collection}()->create(['title' => 'Ficha', 'release_year' => 2020, 'watched_on' => '2024-01-01']);
            $base = '/espacios/'.$this->space->id.'/'.$path;
            $url = $base.'/'.$entry->id;
            $ratingUrl = $url.'/valoracion';
            $this->actingAs($this->angel)->put($ratingUrl, ['score' => 0, 'note' => 'Ángel'])->assertRedirect($base);
            $this->actingAs($this->alejandra)->put($ratingUrl, ['score' => 100, 'note' => 'Ale'])->assertRedirect($base);
            $this->put($ratingUrl, ['score' => 70, 'note' => 'Corregida', 'id' => $entry->ratings()->where('user_id', $this->angel->id)->value('id')])->assertRedirect($base);
            $this->put($ratingUrl, ['score' => 10, 'note' => 'Suplantada', 'user_id' => $this->angel->id])->assertSessionHasErrors('user_id');
            $expected = $entry->ratings()->orderBy('user_id')->get(['user_id', 'score', 'note'])->toArray();
            $this->assertSame([
                ['user_id' => $this->angel->id, 'score' => 0, 'note' => 'Ángel'],
                ['user_id' => $this->alejandra->id, 'score' => 70, 'note' => 'Corregida'],
            ], $expected);

            $this->patchMetadata($url, ['overview' => 'Manual', 'score' => 20, 'note' => 'No guardar', 'user_id' => $this->angel->id])->assertSessionHasErrors('save');
            $this->assertSame($expected, $entry->ratings()->orderBy('user_id')->get(['user_id', 'score', 'note'])->toArray());
            Http::fake(['api.themoviedb.org/3/*' => Http::response([
                'id' => 123, 'title' => 'Ficha', 'name' => 'Ficha', 'release_date' => '2020-01-01', 'first_air_date' => '2020-01-01',
            ])]);
            $this->post($base, ['tmdb_id' => 123, 'candidate_id' => $entry->id])->assertRedirect($url);
            $this->patchMetadata($url, ['watched_on' => '2024-02-01', 'score' => 1, 'note' => 'No guardar'])->assertSessionHasErrors('save');
            $this->assertSame($expected, $entry->ratings()->orderBy('user_id')->get(['user_id', 'score', 'note'])->toArray());
            foreach ([$this->angel, $this->alejandra] as $viewer) {
                $this->actingAs($viewer)->get($url)->assertInertia(fn (Assert $page) => $page
                    ->where('viewerId', $viewer->id)->has('movie.ratings', 2));
            }

            $other = Space::create(['name' => 'Otra '.$collection, 'owner_id' => $this->angel->id]);
            $other->members()->attach($this->angel->id);
            $crossed = '/espacios/'.$other->id.'/'.$path.'/'.$entry->id.'/valoracion';
            $this->put($crossed, ['score' => 1, 'note' => 'Ajena'])->assertNotFound();
            $other->members()->attach($this->alejandra->id);
            $this->put($crossed, ['score' => 1, 'note' => 'Ajena'])->assertNotFound();
            $this->space->members()->detach($this->alejandra->id);
            $this->put($ratingUrl, ['score' => 1, 'note' => 'Ajena'])->assertNotFound();
            $this->space->members()->attach($this->alejandra->id);
            $this->assertSame($expected, $entry->ratings()->orderBy('user_id')->get(['user_id', 'score', 'note'])->toArray());
            $this->actingAs($this->angel)->put($ratingUrl, ['score' => null, 'note' => null])->assertRedirect($base);
            $this->assertSame(2, $entry->ratings()->count());
            $this->assertNull($entry->ratings()->where('user_id', $this->angel->id)->sole()->score);
            $this->assertSame('Corregida', $entry->ratings()->where('user_id', $this->alejandra->id)->sole()->note);
        }
    }

    public function test_movie_and_series_rating_errors_preserve_both_authors_and_input(): void
    {
        foreach (['movies' => 'peliculas', 'series' => 'series'] as $collection => $path) {
            $entry = $this->space->{$collection}()->create(['title' => 'Ficha', 'watched_on' => '2024-01-01']);
            foreach ([$this->angel, $this->alejandra] as $person) {
                $entry->ratings()->create(['user_id' => $person->id, 'score' => 65, 'note' => 'Anterior']);
            }
            $url = '/espacios/'.$this->space->id.'/'.$path.'/'.$entry->id;
            $ratingUrl = $url.'/valoracion';
            $this->actingAs($this->angel)->from($url);
            $this->put($ratingUrl, ['score' => 100, 'note' => str_repeat('a', 5000)])->assertRedirect();
            $before = $entry->ratings()->orderBy('user_id')->get(['user_id', 'score', 'note'])->toArray();
            foreach ([['score' => -1, 'note' => null], ['score' => 101, 'note' => null], ['score' => 4.5, 'note' => null], ['score' => 80, 'note' => str_repeat('a', 5001)]] as $invalid) {
                $this->put($ratingUrl, $invalid)->assertSessionHasErrors();
                $this->assertSame($before, $entry->ratings()->orderBy('user_id')->get(['user_id', 'score', 'note'])->toArray());
            }
            $fail = true;
            $table = $collection === 'movies' ? 'movie_ratings' : 'series_ratings';
            DB::connection()->beforeExecuting(function ($sql) use ($table, &$fail) {
                if ($fail && str_starts_with($sql, 'insert into "'.$table.'"')) {
                    throw new QueryException('sqlite', $sql, [], new \RuntimeException('Fallo simulado'));
                }
            });
            try {
                $this->put($ratingUrl, ['score' => 80, 'note' => 'Borrador'])->assertRedirect($url)
                    ->assertSessionHasErrors('save')->assertSessionHasInput('note', 'Borrador');
                $this->assertSame($before, $entry->ratings()->orderBy('user_id')->get(['user_id', 'score', 'note'])->toArray());
            } finally {
                $fail = false;
            }
        }
    }

    public function test_manual_candidates_require_confirmation_and_allow_distinct_films(): void
    {
        $movie = $this->space->movies()->create(['title' => 'Crash', 'watched_on' => '2024-01-01']);
        $this->actingAs($this->angel)->from($this->moviesUrl.'/nueva');
        $this->post($this->moviesUrl, ['title' => 'Crash', 'watched_on' => '2024-02-01'])->assertSessionHasErrors('release_year');
        $data = ['title' => 'Crash', 'release_year' => 2004, 'watched_on' => '2024-02-01'];
        $this->post($this->moviesUrl, $data)->assertSessionHasErrors('match')->assertSessionHas($this->candidateKey.'.0.id', $movie->id);
        $this->post($this->moviesUrl, $data + ['candidate_id' => $movie->id])->assertRedirect(route('movies.show', [$this->space, $movie]));
        $response = $this->post($this->moviesUrl, $data + ['distinct' => true, 'candidate_ids' => [$movie->id]]);
        $distinct = Movie::where('release_year', 2004)->firstOrFail();
        $response->assertRedirect(route('movies.show', [$this->space, $distinct]));
        $movie->update(['release_year' => 1996]);
        $this->post($this->moviesUrl, $data)->assertSessionHasErrors('match')->assertSessionHas($this->candidateKey.'.0.id', $distinct->id);
        $response = $this->post($this->moviesUrl, array_replace($data, ['release_year' => 2024]));
        $response->assertRedirect(route('movies.show', [$this->space, Movie::where('release_year', 2024)->firstOrFail()]));
    }

    public function test_movie_list_is_paginated_in_watch_order(): void
    {
        foreach (range(1, 13) as $number) {
            $this->space->movies()->create(['title' => 'Película '.$number, 'watched_on' => '2024-01-01']);
        }

        $this->actingAs($this->angel)->get($this->moviesUrl)->assertInertia(fn (Assert $page) => $page
            ->has('movies', 12)->where('movies.0.title', 'Película 13')
            ->where('pagination.page', 1)->where('pagination.last', 2));
        $this->get($this->moviesUrl.'?page=2')->assertInertia(fn (Assert $page) => $page
            ->has('movies', 1)->where('movies.0.title', 'Película 1')->where('pagination.next', null));
        $this->get($this->moviesUrl.'?page=3')->assertRedirect($this->moviesUrl.'?page=2');
    }

    public function test_rating_ownership_and_concurrent_common_edits_and_deletion(): void
    {
        $movie = $this->space->movies()->create(['title' => 'Alien', 'watched_on' => '2024-01-01']);
        $url = $this->moviesUrl.'/'.$movie->id;
        $this->actingAs($this->angel);
        foreach ([-1, 101, 2.5] as $invalid) {
            $this->put($url.'/valoracion', ['score' => $invalid, 'note' => null])->assertSessionHasErrors('score');
        }
        $this->put($url.'/valoracion', ['score' => 0, 'note' => 'Nota A'])->assertRedirect($this->moviesUrl);
        $this->actingAs($this->alejandra)->put($url.'/valoracion', ['score' => 100, 'note' => 'Nota B'])->assertRedirect($this->moviesUrl);
        $this->put($url.'/valoracion', ['score' => 50, 'note' => null, 'user_id' => $this->angel->id])->assertSessionHasErrors('user_id');
        $this->patchMetadata($url, ['title' => 'Alien: el octavo pasajero'])->assertRedirect($url);
        $this->actingAs($this->angel)->patchMetadata($url, ['watched_on' => '2024-01-02'])->assertRedirect($url);
        $this->actingAs($this->alejandra)->patchMetadata($url, ['watched_on' => '2024-01-03'])->assertRedirect($url);
        $this->get($url)->assertInertia(fn (Assert $page) => $page
            ->where('movie.title', 'Alien: el octavo pasajero')->where('movie.watched_on', '2024-01-03')
            ->where('movie.ratings.0.note', 'Nota A')->where('movie.ratings.1.note', 'Nota B'));
        $this->delete($url)->assertSessionHasErrors('confirmed');
        $this->assertDatabaseCount('movie_ratings', 2);
        $this->delete($url, ['confirmed' => true])->assertRedirect($this->moviesUrl);
        $this->actingAs($this->angel)->patchMetadata($url, ['title' => 'Tarde'])->assertNotFound();
        $this->assertDatabaseCount('movies', 0);
        $this->assertDatabaseCount('movie_ratings', 0);
    }

    public function test_catalog_lookup_confirmation_and_manual_fallback(): void
    {
        config(['services.tmdb.token' => 'test-token']);
        $result = ['id' => 44, 'title' => 'Crash', 'release_date' => '2004-01-01', 'poster_path' => '/crash.jpg', 'overview' => ' Una sinopsis en español. '];
        Http::fake([
            'api.themoviedb.org/3/search/movie*' => Http::sequence()->push(['results' => [$result]])->push(['results' => []])->pushStatus(503),
            'api.themoviedb.org/3/movie/44*' => Http::response($result),
            'api.themoviedb.org/3/movie/45*' => Http::response(['id' => 45, 'title' => 'Sin año', 'poster_path' => '//externo.example/imagen.jpg']),
            'api.themoviedb.org/3/movie/46*' => Http::response(['id' => 46, 'title' => 'Seven']),
        ]);
        $this->actingAs($this->angel)->from($this->moviesUrl.'/nueva');
        $this->getJson($this->moviesUrl.'/catalogo?q=Crash')->assertOk()
            ->assertJsonPath('movies.0.release_year', 2004)->assertJsonPath('movies.0.poster_path', '/crash.jpg');
        $this->getJson($this->moviesUrl.'/catalogo?q=Otra')->assertOk()->assertExactJson(['movies' => []]);
        $this->getJson($this->moviesUrl.'/catalogo?q=Caída')->assertUnprocessable()->assertJsonValidationErrors('catalog');
        $first = $this->space->movies()->create(['title' => 'Crash', 'watched_on' => '2024-01-01', 'release_year' => 2004]);
        $second = $this->space->movies()->create(['title' => 'Crash', 'watched_on' => '2024-02-01']);
        $second->ratings()->create(['user_id' => $this->angel->id, 'score' => 80, 'note' => 'Conservar']);
        $this->post($this->moviesUrl, ['tmdb_id' => 44])->assertSessionHasErrors('match')
            ->assertSessionHas($this->candidateKey, fn ($matches) => collect($matches)->pluck('id')->all() === [$first->id, $second->id]);
        $this->post($this->moviesUrl, ['tmdb_id' => 44, 'candidate_id' => $second->id])->assertRedirect(route('movies.show', [$this->space, $second]));
        $this->assertDatabaseHas('movies', ['id' => $second->id, 'watched_on' => '2024-02-01', 'tmdb_id' => 44, 'poster_path' => '/crash.jpg']);
        $this->get(route('movies.show', [$this->space, $second]))->assertInertia(fn (Assert $page) => $page
            ->where('movie.poster_path', '/crash.jpg')->where('movie.overview', 'Una sinopsis en español.'));
        $this->assertDatabaseHas('movies', ['id' => $first->id, 'tmdb_id' => null]);
        $this->assertDatabaseHas('movie_ratings', ['movie_id' => $second->id, 'score' => 80, 'note' => 'Conservar']);
        $this->post($this->moviesUrl, ['tmdb_id' => 44])->assertRedirect(route('movies.show', [$this->space, $second]));
        $response = $this->post($this->moviesUrl, ['title' => 'Sin año', 'release_year' => 2011, 'watched_on' => '2024-03-01']);
        $manual = Movie::where('title', 'Sin año')->firstOrFail();
        $response->assertRedirect(route('movies.show', [$this->space, $manual]));
        $this->post($this->moviesUrl, ['tmdb_id' => 45])->assertSessionHasErrors('match');
        $response = $this->post($this->moviesUrl, ['tmdb_id' => 45, 'distinct' => true, 'candidate_ids' => [$manual->id], 'watched_on' => '2024-04-01']);
        $response->assertRedirect(route('movies.show', [$this->space, Movie::where('tmdb_id', 45)->firstOrFail()]));
        $this->assertDatabaseHas('movies', ['tmdb_id' => 45, 'poster_path' => null, 'overview' => null]);
        $response = $this->post($this->moviesUrl, ['tmdb_id' => 46, 'watched_on' => null]);
        $response->assertRedirect(route('movies.show', [$this->space, Movie::where('tmdb_id', 46)->firstOrFail()]));
        $this->assertDatabaseHas('movies', ['tmdb_id' => 46, 'watched_on' => today()->toDateString()]);
        $this->get('/creditos')->assertInertia(fn (Assert $page) => $page->component('Credits/Credits'));
    }

    public function test_storage_failure_returns_a_retryable_form_error(): void
    {
        Movie::creating(function () {
            throw new QueryException('sqlite', 'test', [], new \RuntimeException('Simulated write failure'));
        });
        $this->actingAs($this->angel)->from($this->moviesUrl.'/nueva')
            ->post($this->moviesUrl, ['title' => 'Arrival', 'watched_on' => '2024-01-01'])
            ->assertRedirect($this->moviesUrl.'/nueva')->assertSessionHasErrors('save');
        $this->assertDatabaseCount('movies', 0);
    }
}
