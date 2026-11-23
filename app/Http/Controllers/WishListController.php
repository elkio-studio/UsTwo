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
                if (DB::getDriverName() === 'sqlite') DB::table('spaces')->where('id', $space->id)->update(['id' => DB::raw('id')]);
                $locked = Space::whereKey($space->id)->lockForUpdate()->firstOrFail();
                abort_unless($locked->members()->whereKey($request->user()->id)->exists(), 404);
                return $locked->wishLists()->create($data);
            }, 3);
        } catch (QueryException) {
            throw ValidationException::withMessages(['save' => 'No se pudo crear la lista. Inténtalo de nuevo.']);
        }
        return response()->json($list->only(['id', 'name']), 201);
    }
}
