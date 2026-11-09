<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

Artisan::command('ustwo:account {email} {name}', function () {
    $email = mb_strtolower(trim($this->argument('email')));
    $data = Validator::make([
        'email' => $email,
        'name' => $this->argument('name'),
        'password' => $this->secret('Contraseña (mínimo 12 caracteres)'),
    ], [
        'email' => 'required|email|max:255',
        'name' => 'required|string|max:255',
        'password' => 'required|string|min:12|max:72',
    ])->validate();

    if ($data['password'] !== $this->secret('Repite la contraseña')) {
        $this->error('Las contraseñas no coinciden.');

        return 1;
    }

    if (! User::where('email', $email)->exists() && User::count() >= 2) {
        $this->error('Ya existen las dos cuentas de UsTwo.');

        return 1;
    }

    User::updateOrCreate(['email' => $email], $data);
    $this->info('Cuenta guardada.');

    return 0;
});
