<?php

namespace Tests\Feature;

use App\Models\Space;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LegacySpaceMigrationTest extends TestCase
{
    public function test_migration_preserves_credentials_movies_and_ratings_in_sqlite(): void
    {
        config(['database.connections.legacy' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true,
        ]]);
        DB::setDefaultConnection('legacy');
        $directory = database_path('migrations');
        $original = require $directory.'/2026_11_09_000000_create_mvp_tables.php';
        $original->up();

        $password = 'legacy-test-fixture';
        $hash = Hash::make($password);
        DB::table('users')->insert([
            ['id' => 10, 'name' => 'Ángel', 'email' => 'angel@gmail.com', 'password' => $hash],
            ['id' => 20, 'name' => 'Alejandra', 'email' => 'alejandra@gmail.com', 'password' => $hash],
        ]);
        DB::table('movies')->insert(['id' => 30, 'title' => 'Alien', 'watched_on' => '2024-01-01', 'tmdb_id' => 42]);
        DB::table('movie_ratings')->insert([
            'id' => 40, 'movie_id' => 30, 'user_id' => 20, 'score' => 92, 'note' => 'Conservar',
        ]);

        $migration = require $directory.'/2026_11_10_000000_create_shared_spaces.php';
        $migration->up();

        $space = Space::sole();
        $this->assertSame(10, $space->owner_id);
        $this->assertSame([10, 20], $space->members()->orderBy('users.id')->pluck('users.id')->all());
        $this->assertSame($hash, User::findOrFail(10)->password);
        $this->assertSame($hash, User::findOrFail(20)->password);
        $this->assertDatabaseHas('users', ['id' => 10, 'username' => 'angl_j_rgl']);
        $this->assertDatabaseHas('users', ['id' => 20, 'username' => 'Aleismowgli']);
        $this->assertDatabaseHas('movies', ['id' => 30, 'space_id' => $space->id, 'tmdb_id' => 42]);
        $this->assertDatabaseHas('movie_ratings', [
            'id' => 40, 'movie_id' => 30, 'user_id' => 20, 'score' => 92, 'note' => 'Conservar',
        ]);

        $this->post('/acceso', ['email' => 'angel@gmail.com', 'password' => $password])->assertRedirect('/');
        $this->assertAuthenticatedAs(User::findOrFail(10));
        $this->post('/salir');
        $this->post('/acceso', ['email' => 'alejandra@gmail.com', 'password' => $password])->assertRedirect('/');
        $this->assertAuthenticatedAs(User::findOrFail(20));
    }
}
