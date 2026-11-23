<?php

namespace Tests\Feature;

use App\Models\Space;
use App\Models\User;
use App\Models\Wish;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class WishesTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Space $space;
    private string $base;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->owner = $this->account('owner');
        $this->space = Space::create(['name' => 'Casa', 'owner_id' => $this->owner->id]);
        $this->space->members()->attach($this->owner);
        $this->base = '/espacios/'.$this->space->id.'/deseos';
    }

    public function test_manual_creation_lists_authorship_and_reload_never_use_the_network(): void
    {
        Http::preventStrayRequests();
        $this->actingAs($this->owner)->get($this->base.'/nuevo')->assertInertia(fn (Assert $page) => $page
            ->component('Wishes/Create')->has('lists', 0));
        $this->assertDatabaseCount('wish_lists', 0);
        $id = $this->postJson($this->base.'/listas', ['name' => 'Cumpleaños'])->assertCreated()->json('id');
        $this->assertDatabaseCount('wishes', 0);
        $this->post($this->base, ['list_id' => $id, 'url' => 'https://example.com/item', 'title' => 'Mi deseo',
            'author_id' => 999, 'space_id' => 999, 'status' => 'bought',
        ])->assertRedirect($this->base.'?list='.$id);
        $this->assertDatabaseHas('wishes', ['wish_list_id' => $id, 'author_id' => $this->owner->id,
            'status' => 'pending', 'price_minor' => null, 'currency' => null]);
        $this->get($this->base.'?list='.$id)->assertInertia(fn (Assert $page) => $page
            ->component('Wishes/Wishes')->where('selectedListId', $id)->has('lists', 1)
            ->where('lists.0.wishes.0.title', 'Mi deseo')->where('lists.0.wishes.0.author', 'owner')
            ->where('lists.0.wishes.0.price', null));
        $this->post($this->base, ['list_id' => $id, 'url' => 'https://example.com/item', 'title' => 'Cero', 'price' => '0', 'currency' => 'EUR'])->assertRedirect();
        $this->post($this->base, ['list_id' => $id, 'url' => 'https://example.com/item', 'title' => 'Precio', 'price' => '19,99'])->assertRedirect();
        $this->get($this->base)->assertInertia(fn (Assert $page) => $page
            ->where('lists.0.wishes.0.price', '19.99')->where('lists.0.wishes.1.price', '0.00'));
        $this->assertDatabaseHas('wishes', ['title' => 'Precio', 'price_minor' => 1999, 'currency' => 'EUR']);
    }

    public function test_validation_preserves_input_and_rejects_other_lists_and_currency_errors(): void
    {
        $other = Space::create(['name' => 'Otra', 'owner_id' => $this->owner->id]);
        $list = $other->wishLists()->create(['name' => 'Privada']);
        $this->actingAs($this->owner)->from($this->base.'/nuevo')->post($this->base, [
            'list_id' => $list->id, 'url' => 'https://example.com/item', 'title' => 'Conservar',
        ])->assertSessionHasErrors('list_id')->assertSessionHasInput('title', 'Conservar');
        $own = $this->space->wishLists()->create(['name' => 'Mía']);
        foreach (['javascript:alert(1)', 'data:text/html,mal', '//example.com', 'https://user:pass@example.com', 'https://127.1', 'https://0x7f000001', 'https://example.com\\@localhost'] as $url) {
            $this->post($this->base, ['list_id' => $own->id, 'url' => $url, 'title' => 'Conservar'])->assertSessionHasErrors('url');
        }
        $valid = ['list_id' => $own->id, 'url' => 'https://example.com', 'title' => 'Conservar'];
        foreach ([['price' => '-1', 'currency' => 'EUR'], ['price' => '1.234', 'currency' => 'EUR'],
            ['price' => '100000000', 'currency' => 'EUR'], ['price' => '1e3', 'currency' => 'EUR'],
            ['price' => '2', 'currency' => 'USD'], ['currency' => 'EUR'], ['price' => '2', 'currency' => 'GBP']] as $invalid) {
            $this->post($this->base, $valid + $invalid)->assertSessionHasErrors();
        }
        $this->post($this->base, $valid + ['price' => '99999999.99'])->assertRedirect();
        $this->assertDatabaseCount('wishes', 1);
        $this->get($this->base.'?list='.$list->id)->assertNotFound();
    }

    public function test_all_routes_require_membership_and_manual_creation_works_without_network(): void
    {
        Http::preventStrayRequests();
        $list = $this->space->wishLists()->create(['name' => 'Mía']);
        foreach (['', '/nuevo'] as $suffix) $this->get($this->base.$suffix)->assertRedirect('/acceso');
        $outsider = $this->account('outsider');
        $this->actingAs($outsider);
        $this->get($this->base)->assertNotFound();
        $this->get($this->base.'/nuevo')->assertNotFound();
        foreach (['', '/listas'] as $suffix) $this->postJson($this->base.$suffix, ['url' => 'https://example.com'])->assertNotFound();
        $this->actingAs($this->owner);
        $this->post($this->base, ['list_id' => $list->id, 'url' => 'https://example.com', 'title' => 'Manual'])->assertRedirect();
        $this->space->members()->detach($this->owner);
        $this->post($this->base, ['list_id' => $list->id, 'url' => 'https://example.com', 'title' => 'Prohibido'])->assertNotFound();
        $this->assertDatabaseCount('wishes', 1);
    }
    public function test_failed_storage_preserves_input_and_creates_no_partial_wish(): void
    {
        $list = $this->space->wishLists()->create(['name' => 'Mía']);
        Wish::creating(function () { throw new QueryException('testing', 'insert wishes', [], new \Exception('failure')); });
        try {
            $this->actingAs($this->owner)->from($this->base.'/nuevo')->post($this->base, [
                'list_id' => $list->id, 'url' => 'https://example.com', 'title' => 'Conservar',
            ])->assertSessionHasErrors('save')->assertSessionHasInput('title', 'Conservar');
            $this->assertDatabaseCount('wishes', 0);
            $this->assertDatabaseCount('wish_lists', 1);
        } finally { Wish::flushEventListeners(); }
    }

    public function test_database_price_constraints_reject_incomplete_pairs(): void
    {
        $list = $this->space->wishLists()->create(['name' => 'Mía']);
        foreach ([['price_minor' => 1], ['currency' => 'EUR'], ['price_minor' => -1, 'currency' => 'EUR'], ['price_minor' => 1, 'currency' => 'JPY'], ['price_minor' => 1, 'currency' => 'USD'], ['price_minor' => 1, 'currency' => 'GBP']] as $invalid) {
            try {
                DB::transaction(fn () => DB::table('wishes')->insert($invalid + [
                    'wish_list_id' => $list->id, 'author_id' => $this->owner->id, 'url' => 'https://example.com', 'title' => 'Error',
                ]));
                $this->fail('Se permitió un precio inválido.');
            } catch (QueryException $error) { $this->assertMatchesRegularExpression('/wishes_(valid_price|eur_only)/', $error->getMessage()); }
        }
    }

    public function test_no_extraction_endpoint_is_exposed(): void
    {
        $this->actingAs($this->owner)->postJson($this->base.'/metadatos', ['url' => 'https://example.com'])->assertNotFound();
        $this->assertDatabaseCount('wish_lists', 0);
        $this->assertDatabaseCount('wishes', 0);
    }
    public function test_additive_migration_keeps_all_previous_tables_and_creates_no_defaults(): void
    {
        $connection = DB::connection();
        $sqlite = $connection->getDriverName() === 'sqlite' && $connection->getDatabaseName() === ':memory:';
        $postgres = $connection->getDriverName() === 'pgsql' && $connection->getDatabaseName() === 'ustwo_ci'
            && $connection->getConfig('host') === '127.0.0.1' && (int) $connection->getConfig('port') === 55432
            && $connection->getConfig('username') === 'ustwo_ci';
        $this->assertTrue(app()->environment('testing') && ($sqlite || $postgres), 'Solo base aislada para pruebas de migración.');
        Space::whereKey($this->space->id)->update(['wish_slot' => 7]);
        $migration = require database_path('migrations/2026_11_23_000013_create_wish_lists_and_wishes.php');
        $migration->down();
        $before = [];
        try {
            foreach (\Illuminate\Support\Facades\Schema::getTableListing(schemaQualified: false) as $table) {
                if ($table === 'migrations') continue;
                $before[$table] = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
            }
        } finally {
            $migration->up();
            (require database_path('migrations/2026_11_23_000014_restrict_wishes_to_euros.php'))->up();
        }
        foreach ($before as $table => $rows) $this->assertSame($rows, DB::table($table)->get()->map(fn ($row) => (array) $row)->all());
        $this->assertSame(7, $this->space->fresh()->wish_slot);
        $this->assertDatabaseCount('wish_lists', 0);
        $this->assertDatabaseCount('wishes', 0);
    }

    public function test_members_edit_move_and_clear_optional_fields_without_changing_authorship(): void
    {
        Http::preventStrayRequests();
        $list = $this->space->wishLists()->create(['name' => 'Origen']);
        $destination = $this->space->wishLists()->create(['name' => 'Destino']);
        $wish = $this->wish($list->id);
        $member = $this->account('member');
        $this->space->members()->attach($member);
        $url = $this->base.'/'.$wish->id;
        $this->actingAs($member)->get($url.'/editar')->assertInertia(fn (Assert $page) => $page
            ->component('Wishes/Edit')->has('lists', 2)->where('wish.id', $wish->id)
            ->where('wish.list_id', $list->id)->where('wish.price', '12.34')
            ->where('wish.description', 'Original'));
        $data = ['list_id' => $destination->id, 'url' => 'https://example.com/changed',
            'title' => '  Corregido  ', 'description' => 'Nueva nota', 'price' => '19,99',
            'author_id' => $member->id, 'status' => 'bought', 'space_id' => 999, 'id' => 999];
        $this->patch($url, $data)->assertRedirect($this->base.'?list='.$destination->id);
        $this->assertDatabaseHas('wishes', ['id' => $wish->id, 'wish_list_id' => $destination->id,
            'author_id' => $this->owner->id, 'status' => 'pending', 'title' => 'Corregido',
            'url' => $data['url'], 'description' => 'Nueva nota', 'price_minor' => 1999, 'currency' => 'EUR']);
        $this->assertDatabaseCount('wishes', 1);
        $this->assertDatabaseCount('wish_lists', 2);
        $this->get($this->base.'?list='.$list->id)->assertInertia(fn (Assert $page) => $page->has('lists.0.wishes', 0));
        foreach (['0', '99999999.99', ''] as $price) {
            $this->patch($url, array_replace($data, ['price' => $price, 'description' => '']))->assertRedirect();
            $this->assertSame(\App\Support\WishPrice::minor($price), $wish->fresh()->price_minor);
        }
        $this->assertNull($wish->fresh()->description);
        $this->assertNull($wish->fresh()->currency);
        foreach (range(1, 2) as $attempt) {
            $this->get($this->base.'?list='.$destination->id)->assertInertia(fn (Assert $page) => $page
                ->where('selectedListId', $destination->id)->where('lists.1.wishes.0.title', 'Corregido')
                ->where('lists.1.wishes.0.price', null));
        }
        Http::assertNothingSent();
    }

    public function test_edit_validation_keeps_every_previous_value_and_input(): void
    {
        Http::preventStrayRequests();
        $list = $this->space->wishLists()->create(['name' => 'Propia']);
        $wish = $this->wish($list->id);
        $before = $wish->fresh()->getAttributes();
        $url = $this->base.'/'.$wish->id;
        $valid = ['list_id' => $list->id, 'url' => 'https://example.com', 'title' => 'Conservar input'];
        $cases = [
            ['title' => '   '], ['title' => str_repeat('x', 256)], ['description' => str_repeat('x', 5001)],
            ['url' => 'javascript:alert(1)'], ['url' => 'https://user:pass@example.com'],
            ['url' => 'https://127.1'], ['url' => 'https://example.com\\@localhost'],
            ['price' => '-1'], ['price' => '1.234'], ['price' => '100000000'], ['price' => '1e3'],
            ['price' => '2', 'currency' => 'USD'], ['currency' => 'EUR'], ['list_id' => 999999],
        ];
        $this->actingAs($this->owner);
        foreach ($cases as $invalid) {
            $this->from($url.'/editar')->patch($url, array_replace($valid, $invalid))
                ->assertSessionHasErrors()->assertSessionHasInput('url', $invalid['url'] ?? $valid['url']);
            $this->assertSame($before, $wish->fresh()->getAttributes());
        }
        Http::assertNothingSent();
    }

    public function test_edit_and_delete_are_scoped_even_when_member_belongs_to_both_spaces(): void
    {
        $list = $this->space->wishLists()->create(['name' => 'Propia']);
        $wish = $this->wish($list->id);
        $other = Space::create(['name' => 'Otra', 'owner_id' => $this->owner->id]);
        $other->members()->attach($this->owner);
        $foreignList = $other->wishLists()->create(['name' => 'Ajena']);
        $foreign = $this->wish($foreignList->id);
        $data = ['list_id' => $foreignList->id, 'url' => 'https://example.com', 'title' => 'No cambiar'];
        $url = $this->base.'/'.$wish->id;
        $this->actingAs($this->owner)->patch($url, $data)->assertSessionHasErrors('list_id');
        foreach ([$foreign->id, 999999] as $id) {
            $this->get($this->base.'/'.$id.'/editar')->assertNotFound();
            $this->patch($this->base.'/'.$id, $data)->assertNotFound();
            $this->delete($this->base.'/'.$id, ['confirmed' => true])->assertNotFound();
        }
        $this->actingAs($this->account('outsider'));
        $this->get($url.'/editar')->assertNotFound();
        $this->patch($url, $data)->assertNotFound();
        $this->delete($url, ['confirmed' => true])->assertNotFound();
        $this->actingAs($this->owner);
        $this->space->members()->detach($this->owner);
        $this->get($url.'/editar')->assertNotFound();
        $this->patch($url, $data)->assertNotFound();
        $this->delete($url, ['confirmed' => true])->assertNotFound();
        $this->app['auth']->guard()->logout();
        $this->get($url.'/editar')->assertRedirect('/acceso');
        $this->patch($url, $data)->assertRedirect('/acceso');
        $this->delete($url, ['confirmed' => true])->assertRedirect('/acceso');
        $this->assertDatabaseCount('wishes', 2);
        $this->assertSame($list->id, $wish->fresh()->wish_list_id);
        $this->assertSame('Original', $wish->fresh()->title);
    }

    public function test_deletion_requires_confirmation_and_keeps_empty_list_and_other_wishes(): void
    {
        Http::preventStrayRequests();
        $list = $this->space->wishLists()->create(['name' => 'Conservar vacía']);
        $other = $this->space->wishLists()->create(['name' => 'Otro']);
        $wish = $this->wish($list->id);
        $kept = $this->wish($other->id);
        $member = $this->account('deleter');
        $this->space->members()->attach($member);
        $url = $this->base.'/'.$wish->id;
        $this->actingAs($member);
        foreach ([[], ['confirmed' => false], ['confirmed' => 'no']] as $data) {
            $this->delete($url, $data)->assertSessionHasErrors('confirmed');
            $this->assertDatabaseCount('wishes', 2);
        }
        $this->delete($url, ['confirmed' => true])->assertRedirect($this->base.'?list='.$list->id);
        $this->assertDatabaseMissing('wishes', ['id' => $wish->id]);
        $this->assertDatabaseHas('wishes', ['id' => $kept->id]);
        $this->assertDatabaseHas('wish_lists', ['id' => $list->id]);
        foreach (range(1, 2) as $attempt) {
            $this->get($this->base.'?list='.$list->id)->assertInertia(fn (Assert $page) => $page
                ->where('selectedListId', $list->id)->has('lists.0.wishes', 0)->has('lists.1.wishes', 1));
        }
        Http::assertNothingSent();
    }

    public function test_sql_failures_roll_back_updates_and_deletions(): void
    {
        $list = $this->space->wishLists()->create(['name' => 'Origen']);
        $destination = $this->space->wishLists()->create(['name' => 'Destino']);
        $wish = $this->wish($list->id);
        $before = $wish->fresh()->getAttributes();
        $url = $this->base.'/'.$wish->id;
        $this->actingAs($this->owner);
        // Fallar después del SQL acredita el rollback, no solo un rechazo previo.
        Wish::updated(function () { throw new QueryException('testing', 'update wishes', [], new \Exception('failure')); });
        try {
            $this->from($url.'/editar')->patch($url, ['list_id' => $destination->id,
                'url' => 'https://example.com/changed', 'title' => 'Conservar', 'price' => '25'])
                ->assertSessionHasErrors('save')->assertSessionHasInput('title', 'Conservar');
            $this->assertSame($before, $wish->fresh()->getAttributes());
        } finally { Wish::flushEventListeners(); }
        Wish::deleted(function () { throw new QueryException('testing', 'delete wishes', [], new \Exception('failure')); });
        try {
            $this->from($this->base.'?list='.$list->id)->delete($url, ['confirmed' => true])
                ->assertSessionHasErrors('delete');
            $this->assertSame($before, $wish->fresh()->getAttributes());
            $this->assertDatabaseCount('wish_lists', 2);
        } finally { Wish::flushEventListeners(); }
    }

    private function wish(int $listId): Wish
    {
        $wish = new Wish(['url' => 'https://example.com/item', 'title' => 'Original',
            'description' => 'Original', 'price_minor' => 1234, 'currency' => 'EUR']);
        $wish->wish_list_id = $listId;
        $wish->author_id = $this->owner->id;
        $wish->status = 'pending';
        $wish->save();

        return $wish;
    }

    private function account(string $username): User
    {
        return User::create(['name' => $username, 'username' => $username, 'username_key' => $username,
            'email' => $username.'@example.test', 'password' => 'test-password']);
    }

    public function test_euro_restriction_refuses_existing_foreign_amounts_without_converting_or_deleting(): void
    {
        $migration = require database_path('migrations/2026_11_23_000014_restrict_wishes_to_euros.php');
        $migration->down();
        DB::beginTransaction();
        try {
            $list = $this->space->wishLists()->create(['name' => 'Anterior']);
            $id = DB::table('wishes')->insertGetId([
                'wish_list_id' => $list->id, 'author_id' => $this->owner->id,
                'url' => 'https://example.com', 'title' => 'No convertir', 'price_minor' => 1000, 'currency' => 'USD',
            ]);
            try {
                $migration->up();
                $this->fail('La migración debe detenerse ante importes en otra moneda.');
            } catch (\RuntimeException $error) {
                $this->assertStringContainsString('no se convierten ni borran', $error->getMessage());
            }
            $this->assertDatabaseHas('wishes', ['id' => $id, 'price_minor' => 1000, 'currency' => 'USD']);
        } finally {
            DB::rollBack();
            $migration->up();
        }
    }
}
