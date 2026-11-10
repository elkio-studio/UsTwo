<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->patch($alien, ['watched_on' => '2025-01-02'])->assertRedirect($alien);
        $this->actingAs($this->angel)->get($alien)->assertInertia(fn (Assert $page) => $page
            ->where('movie.watched_on', '2025-01-02'));
        $this->assertDatabaseCount('movies', 2);
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
        $this->put($url.'/valoracion', ['score' => 0, 'note' => 'Nota A'])->assertRedirect($url);
        $this->actingAs($this->alejandra)->put($url.'/valoracion', ['score' => 100, 'note' => 'Nota B'])->assertRedirect($url);
        $this->put($url.'/valoracion', ['score' => 50, 'note' => null, 'user_id' => $this->angel->id])->assertSessionHasErrors('user_id');
        $this->patch($url, ['title' => 'Alien: el octavo pasajero'])->assertRedirect($url);
        $this->actingAs($this->angel)->patch($url, ['watched_on' => '2024-01-02'])->assertRedirect($url);
        $this->actingAs($this->alejandra)->patch($url, ['watched_on' => '2024-01-03'])->assertRedirect($url);
        $this->get($url)->assertInertia(fn (Assert $page) => $page
            ->where('movie.title', 'Alien: el octavo pasajero')->where('movie.watched_on', '2024-01-03')
            ->where('movie.ratings.0.note', 'Nota A')->where('movie.ratings.1.note', 'Nota B'));
        $this->delete($url)->assertSessionHasErrors('confirmed');
        $this->assertDatabaseCount('movie_ratings', 2);
        $this->delete($url, ['confirmed' => true])->assertRedirect($this->moviesUrl);
        $this->actingAs($this->angel)->patch($url, ['title' => 'Tarde'])->assertNotFound();
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
