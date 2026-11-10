<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class Tmdb
{
    public function search(string $title): array
    {
        $results = $this->get('search/movie', ['query' => $title, 'include_adult' => 'false'])['results'] ?? [];

        return array_values(array_filter(array_map($this->movie(...), $results)));
    }

    public function find(int $id): array
    {
        return $this->movie($this->get("movie/{$id}"))
            ?? throw ValidationException::withMessages(['catalog' => 'Esta ficha no está disponible. Puedes añadirla manualmente.']);
    }

    private function get(string $path, array $query = []): array
    {
        $token = config('services.tmdb.token');

        if ($token) {
            try {
                $result = Http::withToken($token)->acceptJson()->connectTimeout(3)->timeout(6)
                    ->get("https://api.themoviedb.org/3/{$path}", $query + ['language' => 'es-ES'])
                    ->throw()->json();

                if (is_array($result)) {
                    return $result;
                }
            } catch (ConnectionException|RequestException) {
                // Search failure leaves the manual path available.
            }
        }

        throw ValidationException::withMessages(['catalog' => 'El catálogo no está disponible. Puedes añadir la película manualmente.']);
    }

    private function movie(array $result): ?array
    {
        if (empty($result['id']) || empty($result['title']) || ($result['adult'] ?? false)) {
            return null;
        }

        $date = $result['release_date'] ?? '';
        $poster = $result['poster_path'] ?? null;
        $overview = $result['overview'] ?? null;

        return [
            'tmdb_id' => (int) $result['id'],
            'title' => $result['title'],
            'release_year' => preg_match('/^\d{4}-/', $date) ? (int) substr($date, 0, 4) : null,
            'poster_path' => is_string($poster) && preg_match('~\A/[A-Za-z0-9_-]{1,200}\.(?:jpg|jpeg|png|webp)\z~i', $poster)
                ? $poster : null,
            'overview' => is_string($overview) && trim($overview) !== '' ? trim($overview) : null,
        ];
    }
}
