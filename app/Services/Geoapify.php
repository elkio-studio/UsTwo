<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class Geoapify
{
    public function geocode(string $operation, array $parameters): array
    {
        $key = config('services.geoapify.search_key');

        if (! $key) {
            throw ValidationException::withMessages(['search' => 'La búsqueda de lugares no está configurada.']);
        }

        try {
            $response = Http::acceptJson()->connectTimeout(3)->timeout(6)
                ->get('https://api.geoapify.com/v1/geocode/'.$operation, [
                    ...$parameters, 'apiKey' => $key, 'format' => 'json', 'lang' => 'es', 'limit' => 5,
                ]);
        } catch (ConnectionException) {
            throw ValidationException::withMessages(['search' => 'No se pudo conectar con el buscador.']);
        }

        if ($response->status() === 429) {
            throw ValidationException::withMessages(['search' => 'El buscador ha alcanzado su límite. Inténtalo más tarde.']);
        }

        $results = $response->json('results');

        if (! $response->successful() || ! is_array($results)) {
            throw ValidationException::withMessages(['search' => 'El buscador de lugares no está disponible.']);
        }

        return collect($results)
            ->filter(fn ($result) => is_array($result)
                && is_numeric($result['lat'] ?? null) && abs((float) $result['lat']) <= 90
                && is_numeric($result['lon'] ?? null) && abs((float) $result['lon']) <= 180
                && is_string($result['formatted'] ?? null) && trim($result['formatted']) !== '')
            ->map(fn ($result) => [
                'name' => mb_substr(($result['name'] ?? null) ?: $result['formatted'], 0, 255),
                'address' => $result['formatted'],
                'latitude' => (float) $result['lat'],
                'longitude' => (float) $result['lon'],
            ])->values()->all();
    }
}
