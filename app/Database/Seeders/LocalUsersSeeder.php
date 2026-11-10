<?php

namespace App\Database\Seeders;

use App\Models\User;
use App\Models\Space;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LocalUsersSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local') || DB::getDefaultConnection() !== 'sqlite') {
            throw new RuntimeException('Este seeder solo admite SQLite en el entorno local.');
        }

        $accounts = [
            'angel@gmail.com' => ['Ángel', 'angl_j_rgl', 'USTWO_SEED_ANGEL_PASSWORD'],
            'alejandra@gmail.com' => ['Alejandra', 'Aleismowgli', 'USTWO_SEED_ALEJANDRA_PASSWORD'],
        ];

        DB::transaction(function () use ($accounts): void {
            $bootstrap = ! User::exists();
            foreach ($accounts as $email => [$name, $username, $variable]) {
                if (User::where('email', $email)->exists()) {
                    continue;
                }

                $password = env($variable) ?: $this->command?->secret("Contraseña local de {$name}");
                if (! is_string($password) || $password === '') {
                    throw new RuntimeException("Falta la contraseña local de {$name}.");
                }

                User::create([
                    'email' => $email, 'name' => $name, 'password' => $password,
                    'username' => $username, 'username_key' => strtolower($username),
                ]);
            }

            if ($bootstrap) {
                $members = User::whereIn('email', array_keys($accounts))->pluck('id', 'email');
                $space = Space::create(['name' => 'Nuestra estantería', 'owner_id' => $members['angel@gmail.com']]);
                $space->members()->attach($members->values()->all());
            }
        });
    }
}
