<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovieController extends CollectionController
{
    public function legacy(Request $request, ?string $legacy = null)
    {
        $spaces = $request->user()->spaces();
        $space = (clone $spaces)->whereKey($request->session()->get('active_space'))->first()
            ?? $spaces->orderBy('spaces.id')->first();
        abort_unless($space, 404);

        if ($legacy === null || $legacy === 'nueva') {
            return to_route($legacy === null ? 'movies.index' : 'movies.create', $space);
        }

        return to_route('movies.show', [$space, $space->movies()->findOrFail($legacy)]);
    }
}
