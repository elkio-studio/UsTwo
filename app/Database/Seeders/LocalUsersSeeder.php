<?php

namespace App\Database\Seeders;

use App\Models\User;
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
            'angel@gmail.com' => ['Ángel', 'USTWO_SEED_ANGEL_PASSWORD'],
            'alejandra@gmail.com' => ['Alejandra', 'USTWO_SEED_ALEJANDRA_PASSWORD'],
        ];

        DB::transaction(function () use ($accounts): void {
            if (User::whereNotIn('email', array_keys($accounts))->exists()) {
                throw new RuntimeException('La base local ya tiene otras cuentas.');
            }

            foreach ($accounts as $email => [$name, $variable]) {
                if (User::where('email', $email)->exists()) {
                    continue;
                }

                $password = env($variable) ?: $this->command?->secret("Contraseña local de {$name}");
                if (! is_string($password) || $password === '') {
                    throw new RuntimeException("Falta la contraseña local de {$name}.");
                }

                User::create(['email' => $email, 'name' => $name, 'password' => $password]);
            }
        });
    }
}
