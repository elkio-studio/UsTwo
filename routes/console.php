<?php

use App\Models\User;
use App\Models\Movie;
use App\Services\Tmdb;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

Artisan::command('ustwo:movie-metadata {--limit=25}', function (Tmdb $tmdb) {
    $limit = max(1, min(100, (int) $this->option('limit')));
    $ids = Movie::whereNotNull('tmdb_id')
        ->where(fn ($query) => $query->whereNull('poster_path')->orWhereNull('overview'))
        ->distinct()->orderBy('tmdb_id')->limit($limit)->pluck('tmdb_id');
    $updated = ['poster_path' => 0, 'overview' => 0];

    foreach ($ids as $id) {
        try {
            $metadata = $tmdb->find($id);
        } catch (ValidationException) {
            $this->warn('No se ha podido consultar una ficha de TMDB.');
            continue;
        }

        foreach (array_keys($updated) as $field) {
            if ($metadata[$field] !== null) {
                $updated[$field] += Movie::where('tmdb_id', $id)->whereNull($field)->update([$field => $metadata[$field]]);
            }
        }
    }

    $this->info("Portadas actualizadas: {$updated['poster_path']}. Sinopsis actualizadas: {$updated['overview']}.");
})->purpose('Completar portadas y sinopsis de películas vinculadas a TMDB');

Artisan::command('ustwo:account {email} {name} {--username=}', function () {
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

    $account = User::where('email', $email)->first();
    if (! $account) {
        $username = $this->option('username');
        $identity = Validator::make([
            'username' => $username,
            'username_key' => is_string($username) ? strtolower($username) : null,
        ], [
            'username' => ['required', 'string', User::USERNAME_PATTERN],
            'username_key' => 'required|unique:users,username_key',
        ])->validate();
        User::create($data + $identity);
    } else {
        $account->update($data);
    }

    $this->info('Cuenta guardada.');

    return 0;
});
