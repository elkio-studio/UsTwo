<?php

namespace App\Http\Controllers;

use App\Models\Reading;
use App\Models\Space;
use App\Services\ReadingCatalog;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ReadingController extends Controller
{
    public function index(Request $request, Space $space): Response
    {
        return Inertia::render('Readings/Readings', [
            'spaceId' => $space->id,
            'spaceSlug' => $space->slug,
            'activeCategory' => $request->query('category'),
            'people' => $this->people($space),
            'readings' => $space->readings()->latest('id')->get(['id', 'category', 'title', 'cover_url']),
        ]);
    }

    public function create(Space $space, string $category): Response
    {
        return Inertia::render('Readings/Create', [
            'spaceId' => $space->id,
            'category' => $category,
        ]);
    }

    public function show(Space $space, string $readingId): Response
    {
        $reading = $space->readings()->findOrFail($readingId, [
            'id', 'category', 'title', 'description', 'author',
            'publication_date', 'cover_url', 'provider', 'external_id',
        ]);

        return Inertia::render('Readings/Detail', [
            'reading' => $reading,
            'spaceId' => $space->id,
            'people' => $this->people($space),
        ]);
    }

    private function people(Space $space): array
    {
        return $space->members()->orderBy('users.id')->get(['users.id', 'name'])
            ->map->only(['id', 'name'])->all();
    }

    public function search(Request $request, Space $space, ReadingCatalog $catalog): JsonResponse
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(Reading::CATEGORIES)],
            'volume' => ['nullable', 'regex:/^[0-9]{1,20}$/D', 'prohibited_unless:category,comics'],
            'q' => ['nullable', 'required_without:volume', 'string', 'max:120'],
            'page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        $volume = $data['volume'] ?? null;
        $query = $data['q'] ?? '';
        if ($volume !== null && $query !== '' && ! preg_match('/^[\pL\pN.\- ]{1,24}$/uD', $query)) {
            throw ValidationException::withMessages(['q' => 'Introduce un número de cómic válido.']);
        }

        return response()->json($catalog->search($data['category'], $query, (int) ($data['page'] ?? 1), $volume));
    }

    public function preview(Request $request, Space $space, ReadingCatalog $catalog): JsonResponse
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(Reading::CATEGORIES)],
            'id' => ['required', 'string', 'max:80'],
        ]);
        $pattern = $data['category'] === 'books' ? '/^OL[0-9]+W$/D' : '/^[0-9]+$/D';
        if (! preg_match($pattern, $data['id'])) {
            throw ValidationException::withMessages(['catalog' => 'La ficha seleccionada no es válida.']);
        }

        $reading = $catalog->preview($data['category'], $data['id']);

        return response()->json([
            'reading' => $reading,
            'selection' => Crypt::encryptString(json_encode([
                'space' => $space->id,
                'user' => $request->user()->id,
                'expires' => now()->addHour()->timestamp,
                'reading' => $reading,
            ], JSON_THROW_ON_ERROR)),
        ]);
    }

    public function store(Request $request, Space $space): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(Reading::CATEGORIES)],
            'selection' => ['nullable', 'string', 'max:180000'],
            'title' => ['required_without:selection', 'nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:20000'],
            'author' => ['nullable', 'string', 'max:255'],
            'publication_date' => ['nullable', 'date_format:Y-m-d'],
            'confirm_title' => ['nullable', 'boolean'],
        ]);

        $reading = empty($data['selection'])
            ? Arr::only($data, ['category', 'title', 'description', 'author', 'publication_date'])
            : $this->selectedReading($request, $space, $data['category'], $data['selection']);

        $entries = $space->readings();
        if (isset($reading['external_id']) && (clone $entries)
            ->where('provider', $reading['provider'])->where('record_type', $reading['record_type'])
            ->where('external_id', $reading['external_id'])->exists()) {
            $this->duplicate();
        }

        $reading['title_key'] = mb_strtolower(preg_replace('/\s+/u', ' ', trim($reading['title'])));
        if (! $request->boolean('confirm_title') && (clone $entries)->where('title_key', $reading['title_key'])->exists()) {
            throw ValidationException::withMessages(['confirm_title' => 'Ya hay una lectura con ese título. Puedes guardarla igualmente.']);
        }

        try {
            $entry = $entries->create($reading);
        } catch (UniqueConstraintViolationException) {
            $this->duplicate();
        } catch (QueryException $error) {
            report($error);

            throw ValidationException::withMessages(['save' => 'No se pudo guardar la lectura. Inténtalo de nuevo.']);
        }

        return to_route('readings.show', [$space, $entry->id]);
    }

    private function selectedReading(Request $request, Space $space, string $category, string $selection): array
    {
        try {
            $selected = json_decode(Crypt::decryptString($selection), true, flags: JSON_THROW_ON_ERROR);
        } catch (DecryptException|\JsonException) {
            $selected = [];
        }

        if (($selected['space'] ?? null) !== $space->id || ($selected['user'] ?? null) !== $request->user()->id
            || ($selected['expires'] ?? 0) < now()->timestamp || ($selected['reading']['category'] ?? null) !== $category) {
            throw ValidationException::withMessages(['selection' => 'La selección ya no es válida. Vuelve a elegir la ficha del catálogo.']);
        }

        return $selected['reading'];
    }

    private function duplicate(): never
    {
        throw ValidationException::withMessages(['selection' => 'Esta ficha del catálogo ya está guardada en la estantería.']);
    }
}
