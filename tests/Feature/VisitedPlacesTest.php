<?php

namespace Tests\Feature;

use App\Models\Place;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class VisitedPlacesTest extends TestCase
{
    use RefreshDatabase;

    private Space $space;
    private Space $other;
    private string $url;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $user = User::create([
            'name' => 'Member', 'username' => 'member', 'username_key' => 'member',
            'email' => 'member@example.test', 'password' => 'test-password',
        ]);
        $this->space = Space::create(['name' => 'Casa', 'owner_id' => $user->id]);
        $this->other = Space::create(['name' => 'Otra', 'owner_id' => $user->id]);
        $this->space->members()->attach($user);
        $this->url = '/espacios/'.$this->space->id.'/sitios';
        $this->actingAs($user);
        Http::preventStrayRequests();
    }

    public function test_first_save_creates_a_list_and_visited_place_and_later_lists_can_be_empty(): void
    {
        $this->post($this->url.'/listas', ['name' => 'Vacía'])->assertSessionHasErrors('name');
        $data = ['name' => 'Museo', 'latitude' => 37.88123456, 'longitude' => -4.77, 'mode' => 'manual',
            'list_name' => 'Viajes', 'list_emoji' => '🏛️', 'note' => 'Volver', 'status' => 'pending'];
        $this->post($this->url, $data)->assertRedirect($this->url);
        $this->post($this->url, $data)->assertSessionHasErrors('list_id');
        $this->post($this->url.'/listas', ['name' => 'Cafés'])->assertRedirect($this->url);
        $empty = $this->space->placeLists()->where('name', 'Cafés')->sole();
        $this->get($this->url)->assertInertia(fn (Assert $page) => $page->component('Places/Places')
            ->where('lists.0.emoji', '🏛️')->where('lists.0.places.0.status', 'visited')
            ->where('lists.0.places.0.note', 'Volver')->where('lists.0.places.0.latitude', 37.88123456)
            ->has('lists.1.places', 0));
        $this->post($this->url, ['mode' => 'map', 'latitude' => 38, 'longitude' => -5, 'list_id' => $empty->id])
            ->assertRedirect($this->url);
        $this->assertDatabaseHas('places', ['place_list_id' => $empty->id, 'name' => '38, -5', 'status' => 'visited']);
    }

    public function test_saved_places_and_optional_notes_are_read_from_the_current_space(): void
    {
        $local = $this->space->placeLists()->create(['name' => 'Viajes']);
        $foreign = $this->other->placeLists()->create(['name' => 'Otra lista']);
        $position = ['latitude' => 38.25, 'longitude' => -5.5, 'status' => 'visited'];
        $local->places()->create($position + ['name' => 'Museo', 'note' => "Primera línea\n<b>Segunda línea</b>"]);
        $local->places()->create(array_replace($position, ['name' => 'Parque', 'latitude' => 39.25]));
        $foreign->places()->create($position + ['name' => 'Otro sitio']);
        $otherUrl = '/espacios/'.$this->other->id.'/sitios';

        $this->get($otherUrl)->assertNotFound();
        $this->other->members()->attach($this->space->owner_id);
        $this->get($this->url)->assertInertia(fn (Assert $page) => $page->has('lists.0.places', 2));
        $this->get($otherUrl)->assertInertia(fn (Assert $page) => $page
            ->has('lists', 1)->has('lists.0.places', 1)->where('lists.0.places.0.name', 'Otro sitio'));
        $this->get($this->url)->assertInertia(fn (Assert $page) => $page
            ->has('lists', 1)->has('lists.0.places', 2)
            ->where('lists.0.places.0.name', 'Museo')
            ->where('lists.0.places.0.note', "Primera línea\n<b>Segunda línea</b>")
            ->where('lists.0.places.0.latitude', 38.25)
            ->where('lists.0.places.0.longitude', -5.5)
            ->where('lists.0.places.1.name', 'Parque')
            ->where('lists.0.places.1.note', null)
            ->where('lists.0.places.1.latitude', 39.25));
    }

    public function test_invalid_coordinates_and_foreign_lists_cannot_create_places(): void
    {
        $foreign = $this->other->placeLists()->create(['name' => 'Privada']);
        $otherUrl = '/espacios/'.$this->other->id.'/sitios';
        $this->get($otherUrl)->assertNotFound();
        $this->getJson($otherUrl.'/buscar?q=Museo')->assertNotFound();
        $this->post($otherUrl.'/listas', ['name' => 'Intrusa'])->assertNotFound();
        $this->post($otherUrl, [])->assertNotFound();

        $data = ['mode' => 'manual', 'latitude' => 38, 'longitude' => -5, 'name' => 'Sitio'];
        $this->post($this->url, $data + ['list_id' => $foreign->id])->assertSessionHasErrors('list_id');
        $this->post($this->url, $data)->assertSessionHasErrors('list_name');
        $this->post($this->url, array_replace($data, ['latitude' => '1e999', 'longitude' => 181, 'name' => '', 'list_name' => 'Primera']))
            ->assertSessionHasErrors(['latitude', 'longitude', 'name']);
        $this->get($this->url)->assertInertia(fn (Assert $page) => $page->has('lists', 0));
        $this->other->members()->attach($this->space->owner_id);
        $this->get($otherUrl)->assertInertia(fn (Assert $page) => $page
            ->has('lists', 1)->where('lists.0.name', 'Privada'));
        $this->get($this->url)->assertInertia(fn (Assert $page) => $page->has('lists', 0));
        $this->assertDatabaseCount('places', 0);
        $this->assertDatabaseCount('place_lists', 1);
    }

    public function test_failed_first_place_write_rolls_back_the_list_and_reports_the_error(): void
    {
        Place::creating(function () {
            throw new QueryException('test', 'insert into places', [], new \RuntimeException('Escritura fallida'));
        });

        $this->post($this->url, [
            'mode' => 'manual', 'name' => 'Museo', 'latitude' => 38,
            'longitude' => -5, 'list_name' => 'Primera', 'note' => 'Conservar',
        ])->assertSessionHasErrors('save')->assertSessionHasInput('note', 'Conservar');

        $this->assertDatabaseCount('place_lists', 0);
        $this->assertDatabaseCount('places', 0);
    }

    public function test_search_and_reverse_handle_results_empty_responses_and_provider_errors(): void
    {
        config(['services.geoapify.search_key' => 'fake-key']);
        $result = ['name' => 'Museo', 'formatted' => 'Museo, Córdoba', 'lat' => 37.88, 'lon' => -4.77];
        Http::fake([
            'api.geoapify.com/v1/geocode/search*' => Http::sequence()
                ->push(['results' => [$result]])->push(['results' => []])->push(['results' => []])->pushStatus(429)->pushStatus(503),
            'api.geoapify.com/v1/geocode/autocomplete*' => Http::sequence()
                ->push(['results' => [$result, [...$result, 'name' => 'Museo Arqueológico', 'formatted' => 'Museo Arqueológico, Córdoba']]])
                ->push(['results' => [$result]])->push(['results' => []]),
            'api.geoapify.com/v1/geocode/reverse*' => Http::response(['results' => [$result]]),
        ]);
        $this->getJson($this->url.'/buscar?q=Museo')->assertOk()
            ->assertJsonCount(2)->assertJsonPath('0.name', 'Museo')
            ->assertJsonPath('1.name', 'Museo Arqueológico');
        $this->getJson($this->url.'/buscar?q=Muse')->assertOk()->assertJsonPath('0.name', 'Museo');
        $this->getJson($this->url.'/buscar?q=Calle+de+prueba')->assertOk()->assertExactJson([]);
        $this->getJson($this->url.'/buscar?q=Museo')->assertUnprocessable()->assertJsonValidationErrors('search');
        $this->getJson($this->url.'/buscar?q=Museo')->assertUnprocessable()->assertJsonValidationErrors('search');
        $this->getJson($this->url.'/inversa?latitude=37.88&longitude=-4.77')
            ->assertOk()->assertJsonPath('0.address', 'Museo, Córdoba');
        $this->getJson($this->url.'/inversa?latitude=91&longitude=-4.77')->assertUnprocessable();
    }
}
