<?php

namespace Tests\Feature;

use App\Models\Movie;
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->angel = User::create(['name' => 'Ángel', 'email' => 'angel@example.test', 'password' => 'example-password']);
        $this->alejandra = User::create(['name' => 'Alejandra', 'email' => 'alejandra@example.test', 'password' => 'another-example-password']);
        Http::preventStrayRequests();
    }

    public function test_private_pages_and_actions_require_an_individual_session(): void
    {
        $this->get('/')->assertRedirect('/acceso');
        $this->get('/peliculas')->assertRedirect('/acceso');
        $this->getJson('/peliculas/catalogo?q=Alien')->assertUnauthorized();
        $this->post('/peliculas')->assertRedirect('/acceso');
        $this->post('/acceso', ['email' => $this->angel->email, 'password' => 'incorrect'])->assertSessionHasErrors('email');
        $this->post('/acceso', ['email' => $this->angel->email, 'password' => 'example-password'])->assertRedirect();
        $this->assertAuthenticatedAs($this->angel);
        $this->get('/')->assertInertia(fn (Assert $page) => $page->component('Dashboard/Dashboard'));
        $this->post('/salir')->assertRedirect('/acceso');
        $this->get('/peliculas')->assertRedirect('/acceso');
        $this->post('/acceso', ['email' => $this->alejandra->email, 'password' => 'another-example-password'])->assertRedirect();
        $this->assertAuthenticatedAs($this->alejandra);
    }

    public function test_manual_movies_persist_and_are_shared_and_ordered(): void
    {
        $this->actingAs($this->angel)->get('/peliculas')->assertInertia(fn (Assert $page) => $page->has('movies', 0));
        $this->get('/peliculas/nueva')->assertInertia(fn (Assert $page) => $page->component('MovieCreate/MovieCreate'));
        $this->post('/peliculas', ['title' => 'Sin fecha'])->assertSessionHasErrors('watched_on');
        $this->post('/peliculas', ['watched_on' => '2024-01-01'])->assertSessionHasErrors('title');
        $this->post('/peliculas', ['title' => 'Alien', 'watched_on' => '2024-01-01'])->assertRedirect('/peliculas/1');
        $this->actingAs($this->alejandra)->post('/peliculas', ['title' => 'Arrival', 'watched_on' => '2025-01-01'])
            ->assertRedirect('/peliculas/2');
        $this->get('/peliculas')->assertInertia(fn (Assert $page) => $page
            ->where('movies.0.title', 'Arrival')->where('movies.1.title', 'Alien')->missing('movies.1.watched_on'));
        $this->patch('/peliculas/1', ['watched_on' => '2025-01-02'])->assertRedirect('/peliculas/1');
        $this->actingAs($this->angel)->get('/peliculas/1')->assertInertia(fn (Assert $page) => $page
            ->where('movie.watched_on', '2025-01-02'));
        $this->assertDatabaseCount('movies', 2);
    }

    public function test_manual_candidates_require_confirmation_and_allow_distinct_films(): void
    {
        $movie = Movie::create(['title' => 'Crash', 'watched_on' => '2024-01-01']);
        $this->actingAs($this->angel)->from('/peliculas/nueva');
        $this->post('/peliculas', ['title' => 'Crash', 'watched_on' => '2024-02-01'])->assertSessionHasErrors('release_year');
        $data = ['title' => 'Crash', 'release_year' => 2004, 'watched_on' => '2024-02-01'];
        $this->post('/peliculas', $data)->assertSessionHasErrors('match')->assertSessionHas('movie_candidates.0.id', $movie->id);
        $this->post('/peliculas', $data + ['candidate_id' => $movie->id])->assertRedirect('/peliculas/1');
        $this->post('/peliculas', $data + ['distinct' => true, 'candidate_ids' => [$movie->id]])->assertRedirect('/peliculas/2');
        $movie->update(['release_year' => 1996]);
        $this->post('/peliculas', $data)->assertSessionHasErrors('match')->assertSessionHas('movie_candidates.0.id', 2);
        $this->post('/peliculas', array_replace($data, ['release_year' => 2024]))->assertRedirect('/peliculas/3');
    }

    public function test_rating_ownership_and_concurrent_common_edits_and_deletion(): void
    {
        $movie = Movie::create(['title' => 'Alien', 'watched_on' => '2024-01-01']);
        $url = '/peliculas/'.$movie->id;
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
        $this->delete($url, ['confirmed' => true])->assertRedirect('/peliculas');
        $this->actingAs($this->angel)->patch($url, ['title' => 'Tarde'])->assertNotFound();
        $this->assertDatabaseCount('movies', 0);
        $this->assertDatabaseCount('movie_ratings', 0);
    }

    public function test_catalog_lookup_confirmation_and_manual_fallback(): void
    {
        config(['services.tmdb.token' => 'test-token']);
        $result = ['id' => 44, 'title' => 'Crash', 'release_date' => '2004-01-01'];
        Http::fake([
            'api.themoviedb.org/3/search/movie*' => Http::sequence()->push(['results' => [$result]])->push(['results' => []])->pushStatus(503),
            'api.themoviedb.org/3/movie/44*' => Http::response($result),
            'api.themoviedb.org/3/movie/45*' => Http::response(['id' => 45, 'title' => 'Sin año']),
        ]);
        $this->actingAs($this->angel)->from('/peliculas/nueva');
        $this->getJson('/peliculas/catalogo?q=Crash')->assertOk()->assertJsonPath('movies.0.release_year', 2004);
        $this->getJson('/peliculas/catalogo?q=Otra')->assertOk()->assertExactJson(['movies' => []]);
        $this->getJson('/peliculas/catalogo?q=Caída')->assertUnprocessable()->assertJsonValidationErrors('catalog');
        $first = Movie::create(['title' => 'Crash', 'watched_on' => '2024-01-01', 'release_year' => 2004]);
        $second = Movie::create(['title' => 'Crash', 'watched_on' => '2024-02-01']);
        $second->ratings()->create(['user_id' => $this->angel->id, 'score' => 80, 'note' => 'Conservar']);
        $this->post('/peliculas', ['tmdb_id' => 44])->assertSessionHasErrors('match')
            ->assertSessionHas('movie_candidates', fn ($matches) => collect($matches)->pluck('id')->all() === [$first->id, $second->id]);
        $this->post('/peliculas', ['tmdb_id' => 44, 'candidate_id' => $second->id])->assertRedirect('/peliculas/2');
        $this->assertDatabaseHas('movies', ['id' => $second->id, 'watched_on' => '2024-02-01', 'tmdb_id' => 44]);
        $this->assertDatabaseHas('movies', ['id' => $first->id, 'tmdb_id' => null]);
        $this->assertDatabaseHas('movie_ratings', ['movie_id' => $second->id, 'score' => 80, 'note' => 'Conservar']);
        $this->post('/peliculas', ['tmdb_id' => 44])->assertRedirect('/peliculas/2');
        $this->post('/peliculas', ['title' => 'Sin año', 'release_year' => 2011, 'watched_on' => '2024-03-01'])->assertRedirect('/peliculas/3');
        $this->post('/peliculas', ['tmdb_id' => 45])->assertSessionHasErrors('match');
        $this->post('/peliculas', ['tmdb_id' => 45, 'distinct' => true, 'candidate_ids' => [3], 'watched_on' => '2024-04-01'])
            ->assertRedirect('/peliculas/4');
        $this->get('/creditos')->assertInertia(fn (Assert $page) => $page->component('Credits/Credits'));
    }

    public function test_storage_failure_returns_a_retryable_form_error(): void
    {
        Movie::creating(function () {
            throw new QueryException('sqlite', 'test', [], new \RuntimeException('Simulated write failure'));
        });
        $this->actingAs($this->angel)->from('/peliculas/nueva')
            ->post('/peliculas', ['title' => 'Arrival', 'watched_on' => '2024-01-01'])
            ->assertRedirect('/peliculas/nueva')->assertSessionHasErrors('save');
        $this->assertDatabaseCount('movies', 0);
    }
}
