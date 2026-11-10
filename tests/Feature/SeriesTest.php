<?php

namespace Tests\Feature;

use App\Models\Space;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SeriesTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $guest;
    private Space $space;
    private string $url;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->owner = $this->account('owner');
        $this->guest = $this->account('guest');
        $this->space = Space::create(['name' => 'Casa', 'owner_id' => $this->owner->id]);
        $this->space->members()->attach($this->owner->id);
        $this->url = '/espacios/'.$this->space->id.'/series';
        Http::preventStrayRequests();
    }

    public function test_series_catalogue_and_manual_linking_preserve_viewing_and_ratings(): void
    {
        config(['services.tmdb.token' => 'test-token']);
        $catalogue = ['id' => 42, 'name' => 'La serie', 'first_air_date' => '2020-05-01',
            'poster_path' => '/serie.jpg', 'overview' => 'Sinopsis de prueba'];
        Http::fake([
            'api.themoviedb.org/3/search/tv*' => Http::sequence()->push(['results' => [$catalogue]])->pushStatus(503),
            'api.themoviedb.org/3/tv/42*' => Http::response($catalogue),
            'api.themoviedb.org/3/tv/43*' => Http::response(['id' => 43, 'name' => 'Otra serie']),
        ]);
        $this->actingAs($this->owner)->getJson($this->url.'/catalogo?q=serie')
            ->assertOk()->assertJsonPath('movies.0.release_year', 2020)->assertJsonPath('movies.0.poster_path', '/serie.jpg');
        $this->getJson($this->url.'/catalogo?q=Fallo')->assertUnprocessable()->assertJsonValidationErrors('catalog');

        $this->space->movies()->create(['title' => 'Película', 'tmdb_id' => 42, 'watched_on' => '2024-01-01']);
        $this->post($this->url, ['title' => 'La serie', 'release_year' => 2020, 'watched_on' => '2024-02-01'])->assertRedirect();
        $manual = $this->space->series()->sole();
        $manual->ratings()->create(['user_id' => $this->owner->id, 'score' => 90, 'note' => 'Conservar']);
        $this->post($this->url, ['title' => 'La serie', 'watched_on' => '2024-02-02'])->assertSessionHasErrors('release_year');
        $this->post($this->url, ['title' => 'La serie', 'release_year' => 2020, 'watched_on' => '2024-02-02'])->assertSessionHasErrors('match');
        $this->post($this->url, ['tmdb_id' => 42])->assertSessionHasErrors('match');
        $this->post($this->url, ['tmdb_id' => 42, 'candidate_id' => $manual->id])->assertRedirect($this->url.'/'.$manual->id);
        $this->post($this->url, ['tmdb_id' => 42])->assertRedirect($this->url.'/'.$manual->id);
        $this->get($this->url.'/'.$manual->id)->assertInertia(fn (Assert $page) => $page->where('series', true)
            ->where('movie.watched_on', '2024-02-01')->where('movie.poster_path', '/serie.jpg')
            ->where('movie.overview', 'Sinopsis de prueba')->where('movie.ratings.0.note', 'Conservar'));
        $this->post($this->url, ['tmdb_id' => 43])->assertRedirect();
        $this->assertDatabaseHas('series', ['tmdb_id' => 43, 'watched_on' => today()->toDateString()]);
        $this->assertDatabaseCount('series', 2);
        $this->assertDatabaseCount('movies', 1);
    }

    public function test_series_routes_scope_entries_and_ratings_and_paginate(): void
    {
        $series = $this->space->series()->create(['title' => 'Serie inicial', 'watched_on' => '2024-01-01']);
        $detail = $this->url.'/'.$series->id;
        $this->get($detail)->assertRedirect('/acceso');
        $this->actingAs($this->guest)->get($detail)->assertNotFound();
        $this->getJson($this->url.'/catalogo?q=Serie')->assertNotFound();
        $this->post($this->url, ['tmdb_id' => 42])->assertNotFound();
        $other = Space::create(['name' => 'Otra', 'owner_id' => $this->guest->id]);
        $other->members()->attach($this->guest->id);
        $this->space->members()->attach($this->guest->id);
        $this->get('/espacios/'.$other->id.'/series/'.$series->id)->assertNotFound();
        $this->patch($detail, ['title' => 'Editada'])->assertRedirect($detail);
        $this->put($detail.'/valoracion', ['score' => 101, 'note' => null])->assertSessionHasErrors('score');
        $this->put($detail.'/valoracion', ['score' => 90, 'note' => null, 'user_id' => $this->owner->id])->assertSessionHasErrors('user_id');
        $this->put($detail.'/valoracion', ['score' => 90, 'note' => null])->assertRedirect($detail);
        $this->assertDatabaseHas('series_ratings', ['series_id' => $series->id, 'user_id' => $this->guest->id, 'score' => 90]);

        foreach (range(1, 12) as $number) {
            $this->space->series()->create(['title' => "Serie {$number}", 'watched_on' => '2024-02-01']);
        }
        $this->get($this->url)->assertInertia(fn (Assert $page) => $page->where('series', true)
            ->has('movies', 12)->where('movies.0.title', 'Serie 12')->where('pagination.last', 2));
        $this->get($this->url.'?page=2')->assertInertia(fn (Assert $page) => $page->has('movies', 1)->where('movies.0.title', 'Editada'));
        $this->delete($detail)->assertSessionHasErrors('confirmed');
        $this->delete($detail, ['confirmed' => true])->assertRedirect($this->url);
        $this->assertDatabaseMissing('series_ratings', ['series_id' => $series->id]);
    }

    public function test_both_drawers_share_a_shelf_without_collisions_including_old_writers(): void
    {
        $base = '/espacios/'.$this->space->id.'/cajones';
        $this->actingAs($this->guest)->put($base.'/series', ['slot' => 3])->assertNotFound();
        $this->actingAs($this->owner)->put($base.'/peliculas', ['slot' => 2])->assertRedirect('/casa');
        $this->put($base.'/series', ['slot' => 2])->assertSessionHasErrors('slot');
        $this->put($base.'/series', ['slot' => 3])->assertRedirect('/casa');
        $this->put($base.'/peliculas', ['slot' => 3])->assertSessionHasErrors('slot');
        $this->put($base.'/series', ['slot' => 8])->assertRedirect('/casa');
        $this->get('/casa')->assertInertia(fn (Assert $page) => $page
            ->where('activeSpace.movie_slot', 2)->where('activeSpace.series_slot', 8));

        try {
            DB::transaction(fn () => DB::table('spaces')->where('id', $this->space->id)->update(['movie_slot' => 8]));
            $this->fail('La escritura antigua pudo ocupar el hueco de Series.');
        } catch (QueryException $exception) {
            $this->assertStringContainsString('spaces_distinct_slots', $exception->getMessage());
        }
        $this->assertDatabaseHas('spaces', ['id' => $this->space->id, 'movie_slot' => 2, 'series_slot' => 8]);
    }

    private function account(string $username): User
    {
        return User::create([
            'name' => $username, 'username' => $username, 'username_key' => $username,
            'email' => $username.'@example.test', 'password' => 'local-test-fixture',
        ]);
    }
}
