<?php

namespace Tests\Feature;

use App\Models\Space;
use App\Models\SpaceInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SharedSpacesTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_starts_a_session_without_granting_membership(): void
    {
        $this->withoutVite();
        $account = ['name' => 'María', 'email' => 'maria@example.test', 'username' => 'Maria_1', 'password' => 'local-test-fixture'];
        $this->post('/registro', array_replace($account, ['username' => 'María!']))->assertSessionHasErrors('username');
        $this->assertDatabaseCount('users', 0);

        $this->post('/registro', $account)->assertRedirect('/');
        $member = User::where('email', $account['email'])->firstOrFail();
        $this->assertAuthenticatedAs($member);
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('activeSpace', null)->has('spaces', 0));
        $this->post('/salir');

        $this->post('/registro', array_replace($account, ['email' => 'MARIA@example.test', 'username' => 'maria_1']))
            ->assertSessionHasErrors(['email', 'username']);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_invitations_are_private_persistent_and_only_pending_ones_grant_access(): void
    {
        $this->withoutVite();
        $owner = $this->account('owner');
        $guest = $this->account('guest');
        $stranger = $this->account('stranger');
        $this->actingAs($owner)->post('/espacios', ['name' => 'Casa'])->assertRedirect('/casa');
        $space = Space::sole();
        $url = '/espacios/'.$space->id;
        $this->assertSame($owner->id, $space->owner_id);

        $this->post($url.'/invitaciones', ['username' => 'GUEST'])->assertRedirect();
        $first = SpaceInvitation::sole();
        $this->actingAs($stranger)->patch('/invitaciones/'.$first->id, ['response' => 'accepted'])->assertSessionHasErrors('invitation');
        $this->get('/')->assertInertia(fn (Assert $page) => $page->has('invitations', 0));
        $this->actingAs($guest)->get($url.'/peliculas')->assertNotFound();
        $this->post('/salir');
        $this->post('/acceso', ['email' => $guest->email, 'password' => 'local-test-fixture'])->assertRedirect('/');
        $this->travel(120)->days();
        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->has('invitations', 1)->where('invitations.0.id', $first->id)
            ->where('invitations.0.inviter', $owner->username)->where('sessionUsername', $guest->username));
        $this->patch('/invitaciones/'.$first->id, ['response' => 'rejected'])->assertRedirect();
        $this->patch('/invitaciones/'.$first->id, ['response' => 'accepted'])->assertSessionHasErrors('invitation');
        $this->assertDatabaseMissing('space_user', ['space_id' => $space->id, 'user_id' => $guest->id]);

        $this->actingAs($owner)->post($url.'/invitaciones', ['username' => 'guest']);
        $withdrawn = SpaceInvitation::sole();
        $this->delete($url.'/invitaciones/'.$withdrawn->id)->assertRedirect();
        $this->actingAs($guest)->patch('/invitaciones/'.$withdrawn->id, ['response' => 'accepted'])->assertSessionHasErrors('invitation');
        $this->assertDatabaseMissing('space_user', ['space_id' => $space->id, 'user_id' => $guest->id]);

        $this->actingAs($owner)->post($url.'/invitaciones', ['username' => 'guest']);
        $latest = SpaceInvitation::sole();
        $this->actingAs($guest)->patch('/invitaciones/'.$withdrawn->id, ['response' => 'accepted'])->assertSessionHasErrors('invitation');
        $this->patch('/invitaciones/'.$latest->id, ['response' => 'accepted'])->assertRedirect();
        $this->patch('/invitaciones/'.$latest->id, ['response' => 'accepted'])->assertRedirect();
        $this->assertDatabaseCount('space_user', 2);
        $this->get($url.'/peliculas')->assertOk();
        $this->post($url.'/invitaciones', ['username' => 'stranger'])->assertForbidden();
        $this->delete($url.'/invitaciones/'.$latest->id)->assertForbidden();
        $this->delete($url, ['confirmed' => true])->assertForbidden();
        $this->get('/casa')->assertOk();
        $this->delete($url.'/pertenencia')->assertRedirect('/')->assertSessionMissing('active_space');
        $this->get($url.'/peliculas')->assertNotFound();
        $this->get('/casa')->assertNotFound();
        $this->patch('/invitaciones/'.$latest->id, ['response' => 'accepted'])->assertRedirect();
        $this->assertDatabaseMissing('space_user', ['space_id' => $space->id, 'user_id' => $guest->id]);
        $this->actingAs($owner)->delete($url.'/pertenencia')->assertForbidden();
        $this->post($url.'/invitaciones', ['username' => 'guest'])->assertRedirect();
        $this->assertDatabaseHas('space_invitations', ['space_id' => $space->id, 'user_id' => $guest->id, 'status' => 'pending']);
        $pending = SpaceInvitation::where('status', 'pending')->sole();
        $this->actingAs($guest)->patch('/invitaciones/'.$pending->id, ['response' => 'accepted'])->assertRedirect();
        $this->patch($url, ['name' => 'Sala de cine'])->assertForbidden();
        $this->delete($url.'/miembros/'.$owner->id)->assertForbidden();
        $this->actingAs($owner)->patch($url, ['name' => 'Sala de cine'])->assertRedirect('/sala-de-cine');
        $this->get('/sala-de-cine')->assertInertia(fn (Assert $page) => $page->where('members.0.username', $guest->username));
        $this->delete($url.'/miembros/'.$owner->id)->assertForbidden();
        $this->delete($url.'/miembros/'.$guest->id)->assertRedirect('/sala-de-cine');
        $this->actingAs($guest)->get('/sala-de-cine')->assertNotFound();
        $this->get($url.'/peliculas')->assertNotFound();
    }

    public function test_space_scoping_controls_direct_requests_selection_and_cascade_deletion(): void
    {
        $this->withoutVite();
        $owner = $this->account('owner');
        $other = $this->account('other');
        $first = $this->space($owner, 'Casa');
        $second = $this->space($owner, 'Piso');
        $private = $this->space($other, 'Privado');
        $film = ['title' => 'Alien', 'watched_on' => '2024-01-01', 'tmdb_id' => 42];
        $movie = $first->movies()->create($film);
        $privateMovie = $private->movies()->create($film);
        $movie->ratings()->create(['user_id' => $owner->id, 'score' => 91]);
        $privateMovie->ratings()->create(['user_id' => $other->id, 'score' => 75]);

        $firstUrl = '/espacios/'.$first->id;
        $secondUrl = '/espacios/'.$second->id;
        $privateUrl = '/espacios/'.$private->id;
        $this->post('/acceso', ['email' => $owner->email, 'password' => 'local-test-fixture'])->assertRedirect('/');
        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('activeSpace', null)->has('spaces', 2)
            ->where('spaces.0.id', $first->id)->where('spaces.1.id', $second->id));
        $this->put($secondUrl.'/activo')->assertRedirect('/piso');
        $this->get('/piso')->assertInertia(fn (Assert $page) => $page
            ->where('activeSpace.id', $second->id)->has('spaces', 2));
        $this->get('/')->assertInertia(fn (Assert $page) => $page->where('activeSpace', null)->has('spaces', 2));
        $this->get('/privado')->assertNotFound();
        $this->assertSame('casa-2', $this->space($other, 'Cása')->slug);
        $this->assertSame('acceso-2', $this->space($other, 'Acceso')->slug);
        $this->get('/peliculas')->assertRedirect($secondUrl.'/peliculas');
        $this->get('/peliculas/'.$privateMovie->id)->assertNotFound();
        $this->put($privateUrl.'/activo')->assertNotFound();
        $this->get($privateUrl.'/peliculas')->assertNotFound();
        $this->get($privateUrl.'/peliculas/nueva')->assertNotFound();
        $this->getJson($privateUrl.'/peliculas/catalogo?q=Alien')->assertNotFound();
        $this->get($privateUrl.'/peliculas/'.$privateMovie->id)->assertNotFound();
        $this->post($privateUrl.'/peliculas', $film)->assertNotFound();
        $foreignUrl = $firstUrl.'/peliculas/'.$privateMovie->id;
        $this->get($foreignUrl)->assertNotFound();
        $this->patch($foreignUrl, ['title' => 'Cambio'])->assertNotFound();
        $this->put($foreignUrl.'/valoracion', ['score' => 0, 'note' => null])->assertNotFound();
        $this->delete($foreignUrl, ['confirmed' => true])->assertNotFound();
        $this->from($firstUrl.'/peliculas/nueva')
            ->post($firstUrl.'/peliculas', ['title' => 'Alien', 'release_year' => 1979, 'watched_on' => '2024-01-01', 'candidate_id' => $privateMovie->id])
            ->assertSessionHasErrors('match');
        $this->post($firstUrl.'/peliculas', ['title' => 'Solaris', 'watched_on' => '2024-01-01'])->assertRedirect();
        $this->assertDatabaseHas('movies', ['title' => 'Solaris', 'space_id' => $first->id]);
        $this->get($secondUrl.'/peliculas')->assertInertia(fn (Assert $page) => $page->has('movies', 0));

        $this->delete($firstUrl)->assertSessionHasErrors('confirmed');
        $this->assertDatabaseHas('movies', ['id' => $movie->id]);
        $this->delete($firstUrl, ['confirmed' => true])->assertRedirect('/');
        $this->assertDatabaseMissing('movies', ['id' => $movie->id]);
        $this->assertDatabaseMissing('movie_ratings', ['movie_id' => $movie->id]);
        $this->assertDatabaseHas('movie_ratings', ['movie_id' => $privateMovie->id]);
        $this->assertDatabaseCount('users', 2);
    }

    public function test_drawer_placement_requires_membership_and_accepts_only_valid_slots(): void
    {
        $this->withoutVite();
        $owner = $this->account('owner');
        $member = $this->account('member');
        $space = $this->space($owner, 'Casa');
        $url = '/espacios/'.$space->id.'/cajones/peliculas';

        $this->actingAs($member)->put($url, ['slot' => 4])->assertNotFound();
        $space->members()->attach($member->id);
        $this->put($url, ['slot' => 9])->assertSessionHasErrors('slot');
        $this->put($url, ['slot' => 4])->assertRedirect('/casa');
        $this->get('/casa')->assertInertia(fn (Assert $page) => $page->where('activeSpace.movie_slot', 4));
        $this->put($url, ['slot' => 7])->assertRedirect('/casa');
        $this->assertDatabaseHas('spaces', ['id' => $space->id, 'movie_slot' => 7]);
    }

    public function test_account_settings_require_current_password_and_keep_username_unique(): void
    {
        $this->withoutVite();
        $owner = $this->account('owner');
        $member = $this->account('member');
        $space = $this->space($owner, 'Casa');
        $space->members()->attach($member->id);
        $this->get('/mi/cuenta')->assertRedirect('/acceso');
        $this->actingAs($member)->get('/mi/cuenta')->assertInertia(fn (Assert $page) => $page
            ->component('Account/Account')->where('spaces.0.isOwner', false));
        $this->patch('/mi/cuenta', ['username' => 'Nuevo', 'current_password' => 'incorrecta'])
            ->assertSessionHasErrors('current_password');
        $this->patch('/mi/cuenta', ['username' => 'OWNER', 'current_password' => 'local-test-fixture'])
            ->assertSessionHasErrors('username_key');
        $this->patch('/mi/cuenta', [
            'username' => 'Nuevo', 'current_password' => 'local-test-fixture',
            'password' => 'new-test-fixture', 'password_confirmation' => 'new-test-fixture',
        ])->assertRedirect('/mi/cuenta');
        $this->get('/mi/cuenta')->assertInertia(fn (Assert $page) => $page->where('sessionUsername', 'Nuevo'));
        $this->post('/salir');
        $this->post('/acceso', ['email' => $member->email, 'password' => 'new-test-fixture'])->assertRedirect('/');
        $this->delete('/espacios/'.$space->id.'/pertenencia', ['from_account' => true])->assertRedirect('/mi/cuenta');
    }

    private function account(string $username): User
    {
        return User::create([
            'name' => $username, 'username' => $username, 'username_key' => $username,
            'email' => $username.'@example.test', 'password' => 'local-test-fixture',
        ]);
    }

    private function space(User $owner, string $name): Space
    {
        $space = Space::create(['name' => $name, 'owner_id' => $owner->id]);
        $space->members()->attach($owner->id);

        return $space;
    }
}
