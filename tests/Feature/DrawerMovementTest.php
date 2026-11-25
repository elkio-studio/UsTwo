<?php

namespace Tests\Feature;

use App\Models\Space;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DrawerMovementTest extends TestCase
{
    use RefreshDatabase;

    private const DRAWERS = ['peliculas' => 'movie_slot', 'series' => 'series_slot',
        'sitios' => 'places_slot', 'lecturas' => 'reading_slot', 'deseos' => 'wish_slot'];
    private User $owner;
    private Space $space;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->owner = $this->account('owner');
        $this->space = Space::create(['name' => 'Casa', 'owner_id' => $this->owner->id]);
        $this->space->members()->attach($this->owner);
        $this->space->forceFill(array_combine(array_values(self::DRAWERS), range(1, 5)))->save();
    }

    public function test_all_drawers_move_to_empty_and_swap_with_every_other_type(): void
    {
        $this->actingAs($this->owner);
        foreach (self::DRAWERS as $path => $column) {
            foreach (self::DRAWERS as $otherPath => $otherColumn) {
                if ($path === $otherPath) continue;
                $before = $this->positions();
                $this->move($path, $before[$otherColumn], $before)->assertRedirect('/casa');
                $expected = $before;
                $expected[$column] = $before[$otherColumn];
                $expected[$otherColumn] = $before[$column];
                $this->assertSame($expected, $this->positions());
            }
            $before = $this->positions();
            $this->move($path, 8, $before)->assertRedirect('/casa');
            $this->assertSame(8, $this->space->fresh()->$column);
            $this->get('/casa')->assertInertia(fn (Assert $page) => $page->where('activeSpace.'.$column, 8));
            $this->move($path, $before[$column], $this->positions())->assertRedirect('/casa');
        }
    }

    public function test_membership_and_shelf_scoping_are_enforced(): void
    {
        $before = $this->positions();
        $this->move('peliculas', 8, $before)->assertRedirect('/acceso');
        $member = $this->account('member');
        $this->actingAs($member)->move('peliculas', 8, $before)->assertNotFound();
        $this->space->members()->attach($member);
        $other = Space::create(['name' => 'Ajena', 'owner_id' => $this->owner->id]);
        $this->put('/espacios/'.$other->id.'/cajones/mover', $this->payload('peliculas', 8, $before))->assertNotFound();
        $this->move('peliculas', 8, $before)->assertRedirect('/casa');
        $this->assertNull($other->fresh()->movie_slot);
        $this->space->members()->detach($member);
        $this->move('peliculas', 7, $this->positions())->assertNotFound();
        $this->assertSame(8, $this->space->fresh()->movie_slot);
    }

    public function test_invalid_payload_absent_drawer_and_stale_snapshot_do_not_change_positions(): void
    {
        $this->actingAs($this->owner);
        $before = $this->positions();
        foreach ([['drawer' => 'movie_slot'], ['slot' => 0], ['slot' => 9],
            ['slot' => []], ['expected_positions' => []],
            ['expected_positions' => $before + ['arbitrary' => 1]]] as $invalid) {
            $this->put($this->url(), array_replace($this->payload('peliculas', 8, $before), $invalid))->assertSessionHasErrors();
            $this->assertSame($before, $this->positions());
        }
        $duplicate = array_replace($before, ['series_slot' => 1]);
        $this->move('peliculas', 8, $duplicate)->assertSessionHasErrors('movement');
        $missing = $before; unset($missing['wish_slot']);
        $this->move('peliculas', 8, $missing)->assertSessionHasErrors('expected_positions.wish_slot');
        $this->move('peliculas', 8, $before)->assertRedirect('/casa');
        $current = $this->positions();
        $this->move('series', 8, $before)->assertSessionHasErrors('movement');
        $this->assertSame($current, $this->positions());
        $this->space->fresh()->forceFill(['wish_slot' => null])->save();
        $before = $this->positions();
        $this->move('deseos', 7, $before)->assertSessionHasErrors('movement');
        $this->assertSame($before, $this->positions());
        $this->move('series', $before['series_slot'], $before)->assertRedirect('/casa');
        $this->assertSame($before, $this->positions());
    }

    public function test_content_and_other_shelves_are_preserved_and_sql_failure_rolls_back(): void
    {
        $this->actingAs($this->owner);
        $list = $this->space->wishLists()->create(['name' => 'Conservar']);
        DB::table('wishes')->insert(['wish_list_id' => $list->id, 'author_id' => $this->owner->id,
            'url' => 'https://example.com', 'title' => 'Conservar', 'price_minor' => 1000, 'currency' => 'EUR', 'status' => 'pending']);
        $other = Space::create(['name' => 'Otra', 'owner_id' => $this->owner->id]);
        $otherBefore = $other->fresh()->getAttributes();
        foreach (['movies', 'series'] as $table) {
            $id = DB::table($table)->insertGetId(['space_id' => $this->space->id,
                'title' => 'Conservar '.$table, 'watched_on' => '2026-11-25']);
            DB::table($table === 'movies' ? 'movie_ratings' : 'series_ratings')->insert([
                $table === 'movies' ? 'movie_id' : 'series_id' => $id,
                'user_id' => $this->owner->id, 'score' => 8, 'note' => 'Personal']);
        }
        $reading = DB::table('readings')->insertGetId(['space_id' => $this->space->id,
            'category' => 'books', 'title' => 'Conservar libro', 'title_key' => 'conservar libro']);
        DB::table('reading_ratings')->insert(['reading_id' => $reading,
            'user_id' => $this->owner->id, 'score' => 7, 'note' => 'Personal']);
        $places = $this->space->placeLists()->create(['name' => 'Conservar lugares']);
        DB::table('places')->insert(['place_list_id' => $places->id, 'name' => 'Lugar',
            'latitude' => 37.5, 'longitude' => -4.5, 'status' => 'visited', 'note' => 'Conservar']);
        $content = fn () => array_map(fn ($table) => DB::table($table)->orderBy('id')->get()->toArray(),
            ['movies', 'movie_ratings', 'series', 'series_ratings', 'readings',
                'reading_ratings', 'places', 'place_lists', 'wishes', 'wish_lists']);
        $snapshot = $content();
        $this->move('deseos', 1, $this->positions())->assertRedirect('/casa');
        $this->assertEquals($snapshot, $content());
        $this->assertSame($otherBefore, $other->fresh()->getAttributes());
        $before = $this->positions();
        $dispatcher = Space::getEventDispatcher();
        Space::setEventDispatcher(clone $dispatcher);
        Space::updated(function () { throw new QueryException('testing', 'update spaces', [], new \Exception('failure')); });
        try {
            $this->move('deseos', 8, $before)->assertSessionHasErrors('movement');
            $this->assertSame($before, $this->positions());
            $this->assertEquals($snapshot, $content());
        } finally { Space::setEventDispatcher($dispatcher); }
    }

    public function test_existing_placement_contract_still_rejects_occupied_slots(): void
    {
        $this->actingAs($this->owner);
        $before = $this->positions();
        $this->put('/espacios/'.$this->space->id.'/cajones/peliculas', ['slot' => 2])->assertSessionHasErrors('slot');
        $this->assertSame($before, $this->positions());
        $this->put('/espacios/'.$this->space->id.'/cajones/peliculas', ['slot' => 8])->assertRedirect('/casa');
        $this->move('series', 8, $before)->assertSessionHasErrors('movement');
        $this->assertSame(8, $this->space->fresh()->movie_slot);
    }

    private function positions(): array { return $this->space->fresh()->only(array_values(self::DRAWERS)); }
    private function url(): string { return '/espacios/'.$this->space->id.'/cajones/mover'; }
    private function payload(string $drawer, int $slot, array $positions): array
    { return ['drawer' => $drawer, 'slot' => $slot, 'expected_positions' => $positions]; }
    private function move(string $drawer, int $slot, array $positions)
    { return $this->from('/casa')->put($this->url(), $this->payload($drawer, $slot, $positions)); }
    private function account(string $name): User
    { return User::create(['name' => $name, 'username' => $name, 'username_key' => $name,
        'email' => $name.'@example.test', 'password' => 'test-password']); }
}
