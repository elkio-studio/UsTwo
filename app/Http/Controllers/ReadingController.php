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
        $people = $this->people($space);

        return Inertia::render('Readings/Readings', [
            'spaceId' => $space->id,
            'spaceSlug' => $space->slug,
            'activeCategory' => $request->query('category'),
            'people' => $people,
            'readings' => $space->readings()->with([
                'ratings' => fn ($ratings) => $ratings->whereIn('user_id', array_column($people, 'id'))
                    ->select('reading_id', 'user_id', 'score'),
            ])->latest('id')->get(['id', 'category', 'title', 'cover_url']),
        ]);
    }

    public function create(Space $space, string $category): Response
    {
        return Inertia::render('Readings/Create', [
            'spaceId' => $space->id,
            'category' => $category,
        ]);
    }

    public function show(Request $request, Space $space, string $readingId): Response
    {
        $reading = $space->readings()->findOrFail($readingId, [
            'id', 'category', 'title', 'description', 'author',
            'publication_date', 'cover_url', 'provider', 'external_id',
            'catalog_fields', 'metadata_version',
        ]);
        $people = $this->people($space);
        $reading->load([
            'ratings' => fn ($ratings) => $ratings->whereIn('user_id', array_column($people, 'id'))
                ->select('reading_id', 'user_id', 'score', 'note'),
        ]);

        return Inertia::render('Readings/Detail', [
            'reading' => $reading,
            'editableMetadata' => $reading->editableMetadata(),
            'spaceId' => $space->id,
            'people' => $people,
            'viewerId' => $request->user()->id,
        ]);
    }

    public function rating(Request $request, Space $space, string $readingId): RedirectResponse
    {
        $reading = $space->readings()->findOrFail($readingId);
        $data = $request->validate([
            'score' => 'present|nullable|integer|between:0,100',
            'note' => 'present|nullable|string|max:5000',
            'user_id' => 'prohibited',
        ]);

        try {
            $reading->ratings()->upsert(
                [['user_id' => $request->user()->id] + $data],
                ['reading_id', 'user_id'],
                ['score', 'note'],
            );
        } catch (QueryException $error) {
            report($error);

            throw ValidationException::withMessages(['save' => 'No se pudo guardar la valoración. Inténtalo de nuevo.']);
        }

        return to_route('readings.index', ['space' => $space, 'category' => $reading->category]);
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

        $reading['title_key'] = $this->titleKey($reading['title']);
        if (! $request->boolean('confirm_title') && (clone $entries)->where('title_key', $reading['title_key'])->exists()) {
            throw ValidationException::withMessages(['confirm_title' => 'Ya hay una lectura con ese título. Puedes guardarla igualmente.']);
        }

        try {
            $entry = $entries->make($reading);
            $entry->forceFill([
                'catalog_fields' => $entry->provider !== null ? Reading::presentCatalogFields($reading) : [],
                'metadata_version' => 0,
            ])->save();
        } catch (UniqueConstraintViolationException) {
            $this->duplicate();
        } catch (QueryException $error) {
            report($error);

            throw ValidationException::withMessages(['save' => 'No se pudo guardar la lectura. Inténtalo de nuevo.']);
        }

        return to_route('readings.index', ['space' => $space, 'category' => $reading['category']]);
    }

    public function update(Request $request, Space $space, string $readingId): RedirectResponse
    {
        $reading = $space->readings()->findOrFail($readingId);
        $allowed = $reading->editableMetadata();
        $protected = array_diff(array_keys($request->except(['_token', '_method', 'metadata_version'])), $allowed);
        if ($protected !== []) {
            throw ValidationException::withMessages(['save' => 'La solicitud contiene datos que no se pueden modificar.']);
        }

        $changes = $request->validate([
            'metadata_version' => ['required', 'integer', 'min:0'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'author' => ['sometimes', 'nullable', 'string', 'max:255'],
            'publication_date' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'description' => ['sometimes', 'nullable', 'string', 'max:20000'],
        ]);
        $version = (int) $changes['metadata_version'];
        unset($changes['metadata_version']);
        if ($version !== $reading->metadata_version) {
            throw ValidationException::withMessages(['save' => 'La ficha ha cambiado. Recárgala antes de guardar.']);
        }
        $changes = array_filter($changes, fn ($value, $field) => (string) $reading->{$field} !== (string) $value, ARRAY_FILTER_USE_BOTH);

        if (isset($changes['title'])) {
            $changes['title_key'] = $this->titleKey($changes['title']);
        }
        if ($changes !== []) {
            $target = $space->readings()->whereKey($readingId)->where('metadata_version', $version);

            try {
                $updated = $target->update($changes + [
                    'metadata_version' => $version + 1,
                    'catalog_fields' => json_encode($reading->importedMetadata(), JSON_THROW_ON_ERROR),
                ]);
            } catch (QueryException $error) {
                report($error);

                throw ValidationException::withMessages(['save' => 'No se pudo guardar la lectura. Inténtalo de nuevo.']);
            }

            if ($updated === 0) {
                abort_unless($space->readings()->whereKey($readingId)->exists(), 404);
                throw ValidationException::withMessages(['save' => 'La ficha ha cambiado. Recárgala antes de guardar.']);
            }
        }

        return to_route('readings.show', [$space, $readingId]);
    }

    public function destroy(Request $request, Space $space, string $readingId): RedirectResponse
    {
        $reading = $space->readings()->findOrFail($readingId);
        $request->validate(['confirmed' => ['required', 'accepted']]);

        try {
            $deleted = $space->readings()->whereKey($readingId)->delete();
        } catch (QueryException $error) {
            report($error);

            throw ValidationException::withMessages(['delete' => 'No se pudo borrar la lectura. Inténtalo de nuevo.']);
        }
        abort_if($deleted === 0, 404);

        return to_route('readings.index', ['space' => $space, 'category' => $reading->category]);
    }

    private function titleKey(string $title): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($title)));
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
