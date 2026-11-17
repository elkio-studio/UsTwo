<?php

namespace Tests\Feature;

use App\Models\Reading;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ReadingsTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $name = 'angel'): User
    {
        return User::create([
            'name' => $name, 'username' => $name, 'username_key' => $name,
            'email' => $name.'@example.test', 'password' => 'test-password',
        ]);
    }

    private function shelf(string $name, User $member): string
    {
        $space = Space::create(['name' => $name, 'owner_id' => $member->id]);
        $space->members()->attach($member);

        return '/espacios/'.$space->id.'/lecturas';
    }

    private function preview(string $url, string $category, string $id)
    {
        return $this->getJson($url.'/catalogo/ficha?'.http_build_query(compact('category', 'id')))->assertOk();
    }

    private function savedReading(User $member, array $fields = []): array
    {
        $url = $this->shelf('Casa', $member);
        $space = $member->spaces()->firstOrFail();
        $reading = $space->readings()->create($fields + [
            'title' => 'Ficha original',
            'title_key' => 'ficha original',
            'category' => 'manga',
        ]);

        return [$reading, $url, $url.'/'.$reading->id];
    }

    public function test_readings_manual_creation_is_independent_of_catalogs_and_titles_warn(): void
    {
        Http::preventStrayRequests();
        $this->withoutVite();
        $member = $this->account();
        $url = $this->shelf('Casa', $member);
        $other = $this->shelf('Otra', $this->account('ale'));
        $this->get($url)->assertRedirect('/acceso');
        $this->actingAs($member)->get($other)->assertNotFound();
        $this->get($other.'/nueva/books')->assertNotFound();
        $this->get($url.'/nueva/invalid')->assertNotFound();
        $this->get($url.'/nueva/books')->assertInertia(fn (Assert $page) => $page
            ->component('Readings/Create')->where('category', 'books'));
        $this->post($other, ['category' => 'books', 'title' => 'Otro'])->assertNotFound();
        $this->getJson($other.'/catalogo?category=books&q=Otro')->assertNotFound();
        $this->getJson($other.'/catalogo/ficha?category=books&id=OL1W')->assertNotFound();
        $this->from($url)->post($url, ['category' => 'books', 'title' => ' '])->assertSessionHasErrors('title');

        foreach (['books' => 'Harry Potter', 'manga' => 'Naruto', 'comics' => 'Batman #4'] as $category => $title) {
            $this->followingRedirects()->post($url, compact('category', 'title'))
                ->assertInertia(fn (Assert $page) => $page->component('Readings/Readings')
                    ->where('activeCategory', $category)->where('readings.0.title', $title));
        }
        $duplicateTitle = ['category' => 'books', 'title' => 'HARRY   POTTER'];
        $this->from($url)->post($url, $duplicateTitle)->assertSessionHasErrors('confirm_title');
        $this->assertDatabaseCount('readings', 3);
        $this->post($url, $duplicateTitle + ['confirm_title' => true])->assertRedirect($url.'?category=books');
        $this->get($url)->assertInertia(fn (Assert $page) => $page->component('Readings/Readings')
            ->has('readings', 4)
            ->where('readings.1.category', 'comics')->where('readings.1.title', 'Batman #4')
            ->where('readings.2.category', 'manga')->where('readings.2.cover_url', null)
            ->where('readings.3.category', 'books'));
        $reading = Reading::where('title', 'Batman #4')->firstOrFail();
        $this->get($url.'/'.$reading->id)->assertInertia(fn (Assert $page) => $page
            ->component('Readings/Detail')->where('reading.title', 'Batman #4'));
        $this->get($other.'/'.$reading->id)->assertNotFound();
        $memberWithTwoShelves = $this->shelf('Segunda', $member);
        $this->get($memberWithTwoShelves.'/'.$reading->id)->assertNotFound();
        Http::assertNothingSent();
    }

    public function test_book_and_manga_catalogs_return_the_expected_unit_and_metadata(): void
    {
        $member = $this->account();
        $url = $this->shelf('Casa', $member);
        $this->actingAs($member);
        Http::preventStrayRequests();
        Http::fake([
            'openlibrary.org/search.json*' => Http::response(['numFound' => 1, 'docs' => [
                ['key' => '/works/OL1W', 'title' => 'Harry Potter', 'author_name' => ['J. K. Rowling'], 'first_publish_year' => 1997],
            ]]),
            'openlibrary.org/works/OL1W.json' => Http::response([
                'title' => 'Harry Potter', 'description' => ['value' => '<p>Una descripción.</p>'], 'covers' => [12],
                'authors' => [['author' => ['key' => '/authors/OL1A']]],
            ]),
            'api.mangaupdates.com/v1/series/search' => Http::response(['total_hits' => 1, 'results' => [
                ['record' => ['series_id' => 284, 'title' => 'Naruto']],
            ]]),
            'api.mangaupdates.com/v1/series/284' => Http::response([
                'title' => 'Naruto', 'year' => '1999', 'authors' => [['type' => 'Author', 'name' => 'Masashi Kishimoto']],
            ]),
        ]);

        $this->getJson($url.'/catalogo?category=books&q=Harry')->assertOk()->assertJsonPath('items.0.id', 'OL1W');
        $book = $this->preview($url, 'books', 'OL1W')->assertJsonPath('reading.description', 'Una descripción.');
        $this->post($url, ['category' => 'books', 'selection' => $book->json('selection')])->assertRedirect($url.'?category=books');
        $this->assertDatabaseHas('readings', [
            'provider' => 'open_library', 'record_type' => 'work', 'external_id' => 'OL1W',
            'author' => 'J. K. Rowling', 'publication_date' => '1997',
            'cover_url' => 'https://covers.openlibrary.org/b/id/12-M.jpg?default=false',
        ]);

        $this->getJson($url.'/catalogo?category=manga&q=Naruto')->assertOk()->assertJsonPath('items.0.id', '284');
        $manga = $this->preview($url, 'manga', '284')->assertJsonPath('reading.cover_url', null);
        $this->post($url, ['category' => 'manga', 'selection' => $manga->json('selection')])->assertRedirect($url.'?category=manga');
        $this->assertDatabaseHas('readings', [
            'provider' => 'manga_updates', 'record_type' => 'series', 'external_id' => '284',
            'title' => 'Naruto', 'author' => 'Masashi Kishimoto',
        ]);
    }

    public function test_comics_select_an_issue_and_keep_catalog_id_unique_per_shelf(): void
    {
        config(['services.comic_vine.key' => 'test-credential']);
        Http::preventStrayRequests();
        Http::fake([
            'comicvine.gamespot.com/api/search/*' => Http::response(['status_code' => 1, 'results' => [
                ['id' => 796, 'name' => 'Batman', 'start_year' => '1940'],
                ['id' => 91273, 'name' => 'Batman', 'start_year' => '2016', 'publisher' => ['name' => 'DC Comics']],
            ]]),
            'comicvine.gamespot.com/api/issues/*' => Http::response(['status_code' => 1, 'results' => [
                ['id' => 542596, 'name' => 'I Am Gotham Part Four', 'issue_number' => '4', 'volume' => ['name' => 'Batman']],
            ]]),
            'comicvine.gamespot.com/api/issue/4000-542596/*' => Http::response(['status_code' => 1, 'results' => [
                'name' => 'I Am Gotham Part Four', 'issue_number' => '4', 'volume' => ['name' => 'Batman'],
                'cover_date' => '2016-10-31', 'image' => ['original_url' => 'https://example.com/batman.jpg'],
            ]]),
            'comicvine.gamespot.com/api/issue/4000-200/*' => Http::response(['status_code' => 1, 'results' => [
                'name' => 'Recopilatorio', 'issue_number' => '1', 'volume' => ['name' => 'Batman'],
            ]]),
        ]);
        $member = $this->account();
        $url = $this->shelf('Casa', $member);
        $other = $this->shelf('Otra', $member);
        $this->actingAs($member);

        $this->getJson($url.'/catalogo?category=comics&q=Batman')->assertOk()
            ->assertJsonPath('items.1.context', '2016 · DC Comics');
        $this->getJson($url.'/catalogo?category=comics&volume=91273&q=4')->assertOk()
            ->assertJsonPath('items.0.id', '542596');
        Http::assertSent(fn ($request) => str_contains(urldecode($request->url()), 'filter=volume:91273,issue_number:4'));
        $selection = $this->preview($url, 'comics', '542596')->assertJsonPath('reading.cover_url', 'https://example.com/batman.jpg')->json('selection');
        $input = ['category' => 'comics', 'selection' => $selection];
        $this->post($url, $input)->assertRedirect($url.'?category=comics');
        $this->from($url)->post($url, $input + ['confirm_title' => true])->assertSessionHasErrors('selection');
        $this->from($other)->post($other, $input)->assertSessionHasErrors('selection');
        $input['selection'] = $this->preview($other, 'comics', '542596')->json('selection');
        $this->post($other, $input)->assertRedirect($other.'?category=comics');
        $this->assertDatabaseCount('readings', 2);

        $collection = $this->preview($url, 'comics', '200')->json('selection');
        $this->post($url, ['category' => 'comics', 'selection' => $collection])->assertRedirect($url.'?category=comics');
        $this->assertDatabaseCount('readings', 3);
        $this->assertDatabaseHas('readings', ['provider' => 'comic_vine', 'record_type' => 'issue', 'external_id' => '200']);
    }

    public function test_an_empty_catalog_and_rate_limit_keep_manual_entry_available(): void
    {
        $member = $this->account();
        $url = $this->shelf('Casa', $member);
        $this->actingAs($member);
        Http::fake([
            'openlibrary.org/search.json*' => Http::response(['numFound' => 0, 'docs' => []]),
            'api.mangaupdates.com/*' => Http::response([], 429),
        ]);

        $this->getJson($url.'/catalogo?category=books&q=Nada')->assertOk()
            ->assertExactJson(['items' => [], 'hasMore' => false]);
        $this->getJson($url.'/catalogo?category=manga&q=Naruto')->assertUnprocessable()->assertJsonValidationErrors('catalog');
        $this->post($url, ['category' => 'manga', 'title' => 'Naruto'])->assertRedirect($url.'?category=manga');
    }

    public function test_save_failure_returns_a_form_error_and_keeps_input(): void
    {
        $member = $this->account();
        $url = $this->shelf('Casa', $member);
        Reading::creating(function (): never {
            throw new QueryException('test', 'insert into readings', [], new \RuntimeException('Test failure'));
        });

        $this->actingAs($member)->from($url)->post($url, ['category' => 'books', 'title' => 'Un libro'])
            ->assertRedirect($url)
            ->assertSessionHasErrors('save')
            ->assertSessionHasInput('title', 'Un libro');
        $this->assertDatabaseCount('readings', 0);
    }

    public function test_a_member_can_edit_a_manual_reading_and_invalid_fields_leave_it_intact(): void
    {
        $member = $this->account();
        [$reading, $url, $entry] = $this->savedReading($member);
        $this->withoutVite();
        $this->actingAs($member)->from($entry)->patch($entry, [
            'title' => ' ', 'description' => 'Cambio que no debe guardarse',
        ])->assertSessionHasErrors('title');
        $this->assertDatabaseHas('readings', ['id' => $reading->id, 'title' => 'Ficha original', 'description' => null]);

        $this->patch($entry, [
            'title' => 'Nueva   ficha', 'author' => 'Una autora',
            'publication_date' => '2020-09-14', 'description' => 'Sinopsis nueva',
        ])->assertRedirect($entry);
        $this->get($entry)->assertInertia(fn (Assert $page) => $page
            ->where('reading.title', 'Nueva   ficha')->where('reading.author', 'Una autora')
            ->where('reading.publication_date', '2020-09-14')->where('reading.description', 'Sinopsis nueva'));
        $this->get($url.'?category=manga', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => \Inertia\Inertia::getVersion(),
            'X-Inertia-Partial-Component' => 'Readings/Readings',
            'X-Inertia-Partial-Data' => 'readings,people',
        ])->assertOk()->assertJsonPath('props.readings.0.title', 'Nueva   ficha');
        $this->from($url)->post($url, ['category' => 'books', 'title' => 'NUEVA FICHA'])
            ->assertSessionHasErrors('confirm_title');
    }

    public function test_catalog_readings_accept_only_empty_metadata_and_reject_mixed_changes(): void
    {
        $member = $this->account();
        [$reading, , $entry] = $this->savedReading($member, [
            'author' => 'Autoría importada', 'provider' => 'manga_updates', 'external_id' => '27', 'record_type' => 'series',
        ]);
        $this->actingAs($member)->from($entry)->patch($entry, [
            'author' => 'Otro autor', 'description' => 'Sinopsis no guardada',
        ])->assertSessionHasErrors('save');
        $this->patch($entry, ['external_id' => '28'])->assertSessionHasErrors('save');
        $this->patch($entry, ['title' => 'Título distinto'])->assertSessionHasErrors('save');
        $this->assertDatabaseHas('readings', [
            'id' => $reading->id, 'author' => 'Autoría importada', 'description' => null, 'external_id' => '27',
        ]);
        $this->patch($entry, ['description' => 'Sinopsis añadida', 'publication_date' => '2021-04-21'])
            ->assertRedirect($entry);
        $this->patch($entry, ['description' => 'Segunda sinopsis'])->assertSessionHasErrors('save');
        $this->assertDatabaseHas('readings', [
            'id' => $reading->id, 'description' => 'Sinopsis añadida', 'publication_date' => '2021-04-21',
            'provider' => 'manga_updates', 'record_type' => 'series', 'external_id' => '27',
        ]);
    }

    public function test_filling_catalog_fields_rejects_a_conflicting_write_without_partial_changes(): void
    {
        $member = $this->account();
        [$reading, , $entry] = $this->savedReading($member, ['provider' => 'manga_updates']);
        $interleaved = false;
        DB::connection()->beforeExecuting(function ($sql) use (&$interleaved, $reading): void {
            if (! $interleaved && str_starts_with($sql, 'update "readings"')) {
                $interleaved = true;
                Reading::whereKey($reading->id)->update(['author' => 'Otra persona']);
            }
        });

        $this->actingAs($member)->from($entry)->patch($entry, [
            'author' => 'Mi autor', 'description' => 'Sinopsis no guardada',
        ])->assertSessionHasErrors('save');
        $this->assertTrue($interleaved);
        $this->assertDatabaseHas('readings', ['id' => $reading->id, 'author' => 'Otra persona', 'description' => null]);
    }

    public function test_deletion_requires_confirmation_and_both_mutations_are_shelf_scoped(): void
    {
        $member = $this->account();
        [$reading, $url, $entry] = $this->savedReading($member, ['category' => 'comics']);
        $other = $this->shelf('Otra', $member).'/'.$reading->id;
        $this->patch($entry, ['title' => 'Otro'])->assertRedirect('/acceso');
        $this->delete($entry, ['confirmed' => true])->assertRedirect('/acceso');
        $this->actingAs($this->account('ale'))->patch($entry, ['title' => 'Otro'])->assertNotFound();
        $this->delete($entry, ['confirmed' => true])->assertNotFound();
        $this->actingAs($member)->patch($other, ['title' => 'Otro'])->assertNotFound();
        $this->delete($other, ['confirmed' => true])->assertNotFound();
        $this->from($entry)->delete($entry, ['confirmed' => false])->assertSessionHasErrors('confirmed');
        $this->assertDatabaseHas('readings', ['id' => $reading->id, 'title' => 'Ficha original']);

        $this->withoutVite();
        $this->delete($entry, ['confirmed' => true])->assertRedirect($url.'?category=comics');
        $this->get($url.'?category=comics')->assertInertia(fn (Assert $page) => $page
            ->where('activeCategory', 'comics')->has('readings', 0));
        $this->get($entry)->assertNotFound();
    }

    public function test_database_errors_preserve_the_reading_and_return_form_errors(): void
    {
        $member = $this->account();
        [$reading, , $entry] = $this->savedReading($member);
        DB::connection()->beforeExecuting(function ($sql): void {
            if (str_starts_with($sql, 'update "readings"') || str_starts_with($sql, 'delete from "readings"')) {
                throw new QueryException('test', $sql, [], new \RuntimeException('Test failure'));
            }
        });

        $this->actingAs($member)->from($entry)->patch($entry, ['title' => 'Cambio'])
            ->assertRedirect($entry)->assertSessionHasErrors('save')->assertSessionHasInput('title', 'Cambio');
        $this->delete($entry, ['confirmed' => true])->assertRedirect($entry)->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('readings', ['id' => $reading->id, 'title' => 'Ficha original']);
    }
}
