<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Services\Geoapify;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PlaceController extends Controller
{
    public function index(Space $space)
    {
        return Inertia::render('Places/Places', [
            'spaceSlug' => $space->slug,
            'spaceId' => $space->id,
            'lists' => $space->placeLists()->orderBy('id')
                ->with('places:id,place_list_id,name,latitude,longitude,note,status')
                ->get(['id', 'name', 'emoji']),
            'tilesKey' => config('services.geoapify.tiles_key'),
        ]);
    }

    public function search(Request $request, Space $space, Geoapify $geoapify)
    {
        $data = $request->validate(['q' => 'required|string|max:255']);

        return response()->json($geoapify->geocode('search', ['text' => $data['q']]));
    }

    public function reverse(Request $request, Space $space, Geoapify $geoapify)
    {
        $point = $request->validate($this->coordinates());

        return response()->json($geoapify->geocode('reverse', [
            'lat' => $point['latitude'], 'lon' => $point['longitude'],
        ]));
    }

    public function store(Request $request, Space $space)
    {
        $data = $request->validate([
            ...$this->coordinates(),
            'mode' => 'required|in:search,map,manual',
            'name' => 'required_unless:mode,map|nullable|string|max:255',
            'note' => 'nullable|string|max:5000',
            'list_id' => 'nullable|integer|min:1',
            'list_name' => 'required_without:list_id|nullable|string|max:255',
            'list_emoji' => 'nullable|string|max:32',
        ]);

        $this->writeForSpace($space, function () use ($space, $data) {
            $lists = $space->placeLists();

            if (isset($data['list_id'])) {
                $list = $lists->find($data['list_id']);
                if (! $list) {
                    throw ValidationException::withMessages(['list_id' => 'Elige una lista de esta estantería.']);
                }
            } else {
                if ($lists->exists()) {
                    throw ValidationException::withMessages(['list_id' => 'Ya hay listas disponibles. Elige una para guardar el sitio.']);
                }
                $list = $lists->create(['name' => $data['list_name'], 'emoji' => $data['list_emoji'] ?? null]);
            }

            $list->places()->create([
                'name' => $data['name'] ?? $data['latitude'].', '.$data['longitude'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'note' => $data['note'] ?? null,
                'status' => 'visited',
            ]);
        });

        return to_route('places.index', $space);
    }

    public function storeList(Request $request, Space $space)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'emoji' => 'nullable|string|max:32']);

        $this->writeForSpace($space, function () use ($space, $data) {
            if (! $space->placeLists()->exists()) {
                throw ValidationException::withMessages(['name' => 'Crea la primera lista al guardar un sitio.']);
            }

            $space->placeLists()->create($data);
        });

        return to_route('places.index', $space);
    }

    private function coordinates(): array
    {
        return [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ];
    }

    private function writeForSpace(Space $space, Closure $write): void
    {
        try {
            DB::transaction(function () use ($space, $write) {
                // SQLite adquiere el bloqueo de escritura antes de consultar las listas.
                if (DB::getDriverName() === 'sqlite') {
                    DB::table('spaces')->where('id', $space->id)->update(['id' => DB::raw('id')]);
                } else {
                    Space::whereKey($space->id)->lockForUpdate()->firstOrFail();
                }

                $write();
            }, 3);
        } catch (QueryException) {
            throw ValidationException::withMessages(['save' => 'No se pudo guardar. Inténtalo de nuevo.']);
        }
    }
}
