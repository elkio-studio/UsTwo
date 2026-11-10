<?php

namespace App\Database\Seeders;

use App\Models\User;
use App\Models\Space;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class ProductionUsersSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->isProduction() || DB::getDefaultConnection() !== 'pgsql'
            || $this->command === null || $this->command->option('no-interaction')) {
            throw new RuntimeException('Usa este seeder de forma interactiva con PostgreSQL de producción.');
        }

        $accounts = [];
        foreach (['Ángel', 'Alejandra'] as $name) {
            $email = mb_strtolower(trim((string) $this->command->ask("Correo de {$name}")));
            Validator::make(['email' => $email], ['email' => 'required|email|max:255'])->validate();

            if (isset($accounts[$email])) {
                throw new RuntimeException('Las dos cuentas deben tener correos distintos.');
            }

            $accounts[$email] = $name;
        }

        $existing = User::pluck('email')->all();
        if (array_diff($existing, array_keys($accounts))) {
            throw new RuntimeException('Los correos no coinciden con las cuentas existentes.');
        }

        $passwords = [];
        foreach (array_diff(array_keys($accounts), $existing) as $email) {
            $name = $accounts[$email];
            $password = env('PASSWORD_ACCOUNT');
            if ($password === null || $password === '') {
                $password = $this->command->secret("Contraseña de {$name} (mínimo 12 caracteres)", false);
                $confirmation = $this->command->secret("Repite la contraseña de {$name}", false);
            } else {
                $confirmation = $password;
            }
            if (! is_string($password) || mb_strlen($password) < 12
                || strlen($password) > 72 || $password !== $confirmation) {
                throw new RuntimeException('La contraseña no cumple los requisitos o no coincide.');
            }
            $passwords[$email] = $password;
        }

        DB::transaction(function () use ($accounts, $passwords): void {
            DB::statement('LOCK TABLE users IN SHARE ROW EXCLUSIVE MODE');
            $existing = User::pluck('email')->all();
            if (array_diff($existing, array_keys($accounts))
                || array_diff(array_keys($accounts), $existing, array_keys($passwords))) {
                throw new RuntimeException('Las cuentas cambiaron durante el aprovisionamiento.');
            }
            foreach (array_diff(array_keys($accounts), $existing) as $email) {
                $username = $accounts[$email] === 'Ángel' ? 'angl_j_rgl' : 'Aleismowgli';
                User::create([
                    'email' => $email, 'name' => $accounts[$email], 'password' => $passwords[$email],
                    'username' => $username, 'username_key' => strtolower($username),
                ]);
            }
            if ($existing === []) {
                $members = User::whereIn('email', array_keys($accounts))->pluck('id', 'email');
                $space = Space::create(['name' => 'Nuestra estantería', 'owner_id' => $members[array_key_first($accounts)]]);
                $space->members()->attach($members->values()->all());
            }
        });
    }
}
