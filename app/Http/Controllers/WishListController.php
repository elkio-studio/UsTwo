<?php

namespace App\Http\Controllers;

use App\Models\Space;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WishListController extends Controller
{
    public function store(Request $request, Space $space)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        try {
            $list = DB::transaction(function () use ($space, $request, $data) {
                $locked = $this->lockSpace($request, $space);
                return $locked->wishLists()->create($data);
            }, 3);
        } catch (QueryException) {
            throw ValidationException::withMessages(['save' => 'No se pudo crear la lista. Inténtalo de nuevo.']);
        }
        return response()->json($list->only(['id', 'name']), 201);
    }

    public function update(Request $request, Space $space, int $listId)
    {
        $space->wishLists()->findOrFail($listId);
        $data = $request->validate(['name' => 'required|string|max:255']);
        try {
            $selected = DB::transaction(function () use ($request, $space, $listId, $data) {
                $locked = $this->lockSpace($request, $space);
                $list = $locked->wishLists()->lockForUpdate()->findOrFail($listId);
                $list->update(['name' => $data['name']]);

                return $this->selectedList($locked, $request->input('selected_list_id'));
            }, 3);
        } catch (QueryException) {
            throw ValidationException::withMessages(['save' => 'No se pudo cambiar el nombre. Conservamos los datos para que puedas reintentarlo.']);
        }

        return to_route('wishes.index', ['space' => $space->id, 'list' => $selected]);
    }

    public function destroy(Request $request, Space $space, int $listId)
    {
        $space->wishLists()->findOrFail($listId);
        $data = $request->validate([
            'confirmed' => 'required|accepted',
            'confirmed_count' => 'required|integer|min:0',
        ]);
        try {
            $selected = DB::transaction(function () use ($request, $space, $listId, $data) {
                $locked = $this->lockSpace($request, $space);
                $list = $locked->wishLists()->lockForUpdate()->findOrFail($listId);
                if ($list->wishes()->count() !== (int) $data['confirmed_count']) {
                    throw ValidationException::withMessages(['confirmed_count' => 'Ha cambiado el número de deseos de esta lista. Revísala y vuelve a confirmar antes de borrarla.']);
                }
                $list->delete();

                return $this->selectedList($locked, $request->input('selected_list_id'));
            }, 3);
        } catch (QueryException) {
            throw ValidationException::withMessages(['delete' => 'No se pudo eliminar la lista. La lista y sus deseos siguen guardados.']);
        }

        return to_route('wishes.index', ['space' => $space->id, 'list' => $selected]);
    }

    private function lockSpace(Request $request, Space $space): Space
    {
        if (DB::getDriverName() === 'sqlite') DB::table('spaces')->where('id', $space->id)->update(['id' => DB::raw('id')]);
        $locked = Space::whereKey($space->id)->lockForUpdate()->firstOrFail();
        abort_unless($locked->members()->whereKey($request->user()->id)->exists(), 404);

        return $locked;
    }

    private function selectedList(Space $space, mixed $selected): ?int
    {
        if ((is_string($selected) || is_int($selected)) && ctype_digit((string) $selected)) {
            $id = $space->wishLists()->whereKey($selected)->value('id');
            if ($id !== null) return (int) $id;
        }

        return $space->wishLists()->orderBy('id')->value('id');
    }
}
