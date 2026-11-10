<?php

namespace Tests\Feature;

use App\Database\Seeders\ProductionUsersSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeploymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_seeder_uses_the_shared_password_for_both_accounts(): void
    {
        if (DB::getDefaultConnection() !== 'pgsql') {
            $this->markTestSkipped('El seeder de producción requiere PostgreSQL aislado.');
        }

        app()->instance('env', 'production');
        $environment = Env::getRepository();
        $password = 'shared-test-fixture';

        try {
            $environment->set('PASSWORD_ACCOUNT', $password);
            $this->artisan('db:seed', ['--class' => ProductionUsersSeeder::class, '--force' => true])
                ->expectsQuestion('Correo de Ángel', 'angel@example.test')
                ->expectsQuestion('Correo de Alejandra', 'alejandra@example.test')
                ->assertSuccessful();

            $accounts = User::pluck('password', 'email');
            $this->assertTrue(Hash::check($password, $accounts['angel@example.test']));
            $this->assertTrue(Hash::check($password, $accounts['alejandra@example.test']));
        } finally {
            $environment->clear('PASSWORD_ACCOUNT');
        }
    }

    public function test_production_health_checks_the_database_without_disclosing_errors(): void
    {
        app()->instance('env', 'production');
        $this->getJson('/up')->assertOk()->assertExactJson(['status' => 'up']);

        config(['app.debug' => false]);
        DB::shouldReceive('select')->with('select 1')->andThrow(new \RuntimeException('private database details'));
        $this->get('/up')->assertStatus(500)->assertDontSee('private database details');
    }

    public function test_authenticated_session_is_written_to_the_database(): void
    {
        config(['session.driver' => 'database']);
        $user = User::create([
            'name' => 'Ángel',
            'username' => 'angel',
            'username_key' => 'angel',
            'email' => 'angel@example.test',
            'password' => 'session-fixture',
        ]);

        $this->post('/acceso', ['email' => $user->email, 'password' => 'session-fixture'])
            ->assertRedirect('/');
        $session = DB::table('sessions')->where('user_id', $user->id)->first();
        $this->assertNotNull($session);
        $this->withCookie(config('session.cookie'), $session->id)
            ->post('/salir')->assertRedirect('/acceso');
        $this->assertDatabaseMissing('sessions', ['id' => $session->id]);
    }

    public function test_production_seeder_creates_two_accounts_and_preserves_existing_credentials(): void
    {
        if (DB::getDefaultConnection() !== 'pgsql') {
            $this->markTestSkipped('El seeder de producción requiere PostgreSQL aislado.');
        }

        app()->instance('env', 'production');
        $command = ['--class' => ProductionUsersSeeder::class, '--force' => true];
        $environment = Env::getRepository();
        $environment->set('PASSWORD_ACCOUNT', '');
        try {
            $this->artisan('db:seed', $command)
                ->expectsQuestion('Correo de Ángel', 'angel@example.test')
                ->expectsQuestion('Correo de Alejandra', 'alejandra@example.test')
                ->expectsQuestion('Contraseña de Ángel (mínimo 12 caracteres)', 'test-fixture-one')
                ->expectsQuestion('Repite la contraseña de Ángel', 'test-fixture-one')
                ->expectsQuestion('Contraseña de Alejandra (mínimo 12 caracteres)', 'test-fixture-two')
                ->expectsQuestion('Repite la contraseña de Alejandra', 'test-fixture-two')
                ->assertSuccessful();
        } finally {
            $environment->clear('PASSWORD_ACCOUNT');
        }

        $accounts = User::orderBy('email')->pluck('password', 'email')->all();
        $this->assertCount(2, $accounts);
        $this->assertTrue(Hash::check('test-fixture-one', $accounts['angel@example.test']));
        $this->assertTrue(Hash::check('test-fixture-two', $accounts['alejandra@example.test']));

        $this->artisan('db:seed', $command)
            ->expectsQuestion('Correo de Ángel', 'angel@example.test')
            ->expectsQuestion('Correo de Alejandra', 'alejandra@example.test')
            ->assertSuccessful();

        $this->assertSame($accounts, User::orderBy('email')->pluck('password', 'email')->all());
    }
}
