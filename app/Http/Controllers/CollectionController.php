<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Services\Tmdb;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

abstract class CollectionController extends Controller
{
    protected const SERIES = false;
    protected const ROUTE = 'movies';
    protected const CANDIDATES = 'movie_candidates';
    protected const FOREIGN_KEY = 'movie_id';

    private function entries(Space $space)
    {
        return static::SERIES ? $space->series() : $space->movies();
    }

    public function index(Space $space)
    {
        $movies = $this->entries($space)->with('ratings:id,'.static::FOREIGN_KEY.',user_id,score')
            ->orderByDesc('watched_on')->orderByDesc('id')->paginate(12, ['id', 'title', 'poster_path']);

        if ($movies->currentPage() > $movies->lastPage()) {
            return redirect($movies->url($movies->lastPage()));
        }

        return Inertia::render('Movies/Movies', [
            'series' => static::SERIES, 'spaceId' => $space->id,
            'spaceSlug' => $space->slug,
            'movies' => $movies->items(),
            'pagination' => [
                'page' => $movies->currentPage(),
                'last' => $movies->lastPage(),
                'previous' => $movies->previousPageUrl(),
                'next' => $movies->nextPageUrl(),
            ],
            'people' => $space->members()->orderBy('users.id')->get(['users.id', 'name'])->map->only(['id', 'name']),
        ]);
    }

    public function create(Request $request, Space $space)
    {
        return Inertia::render('MovieCreate/MovieCreate', ['series' => static::SERIES, 'spaceId' => $space->id, 'candidates' => $request->session()->get(static::CANDIDATES.'.'.$space->id, [])]);
    }

    public function search(Request $request, Space $space, Tmdb $tmdb)
    {
        $query = $request->validate(['q' => 'required|string|max:255']);

        return response()->json(['movies' => $tmdb->search($query['q'], static::SERIES)]);
    }

    public function show(Request $request, Space $space, int $entry)
    {
        $movie = $this->entries($space)->findOrFail($entry);

        return Inertia::render('MovieDetail/MovieDetail', [
            'series' => static::SERIES, 'spaceId' => $space->id,
            'movie' => $movie->load('ratings'),
            'people' => $space->members()->orderBy('users.id')->get(['users.id', 'name'])->map->only(['id', 'name']),
            'viewerId' => $request->user()->id,
        ]);
    }

    public function store(Request $request, Space $space, Tmdb $tmdb)
    {
        $input = $request->validate([
            'tmdb_id' => 'nullable|integer|min:1',
            'candidate_id' => 'nullable|integer|min:1',
            'distinct' => 'sometimes|boolean',
            'candidate_ids' => 'sometimes|array',
            'candidate_ids.*' => 'integer|min:1',
        ]);
        $tmdbId = $input['tmdb_id'] ?? null;

        if ($tmdbId && ($existing = $this->entries($space)->where('tmdb_id', $tmdbId)->first())) {
            return to_route(static::ROUTE.'.show', [$space, $existing]);
        }

        $metadata = $tmdbId
            ? $tmdb->find($tmdbId, static::SERIES)
            : $request->validate([
                'title' => 'required|string|max:255',
                'release_year' => 'nullable|integer|between:1000,9999',
            ]);
        $year = $metadata['release_year'] ?? null;

        try {
            return DB::transaction(function () use ($request, $space, $input, $tmdbId, $metadata, $year) {
                $sameTitle = $this->entries($space)->when($tmdbId, fn ($query) => $query->whereNull('tmdb_id'))
                    ->orderBy('id')->get(['id', 'title', 'release_year', 'watched_on'])
                    ->filter(fn ($movie) => mb_strtolower($movie->title) === mb_strtolower($metadata['title']));

                if (! $tmdbId && $year === null && $sameTitle->isNotEmpty()) {
                    throw ValidationException::withMessages(['release_year' => 'Indica el año de estreno para distinguir títulos iguales.']);
                }

                $candidates = $sameTitle->filter(fn ($movie) => $year === null
                    || $movie->release_year === null || (int) $movie->release_year === (int) $year)->values();

                if ($candidateId = $input['candidate_id'] ?? null) {
                    $candidate = $candidates->firstWhere('id', $candidateId);
                    if ($candidate && (! $tmdbId || $this->entries($space)->whereKey($candidateId)->whereNull('tmdb_id')->update([
                        'tmdb_id' => $tmdbId,
                        'poster_path' => $metadata['poster_path'],
                        'overview' => $metadata['overview'],
                    ]))) {
                        return to_route(static::ROUTE.'.show', [$space, $candidate]);
                    }
                }

                $reviewedIds = collect($input['candidate_ids'] ?? [])->map(fn ($id) => (int) $id)->sort()->values()->all();
                if ($candidates->isNotEmpty() && (! ($input['distinct'] ?? false)
                    || $reviewedIds !== $candidates->pluck('id')->sort()->values()->all())) {
                    return back()->with(static::CANDIDATES.'.'.$space->id, $candidates)
                        ->withErrors(['match' => 'Confirma si corresponde a la misma ficha.']);
                }

                if (! empty($input['candidate_id'])) {
                    throw ValidationException::withMessages(['match' => 'La entrada elegida ya no está disponible. Revisa la selección.']);
                }

                $date = $request->validate([
                    'watched_on' => ($tmdbId ? 'nullable' : 'required').'|date_format:Y-m-d|before_or_equal:today',
                ]);
                $movie = $this->entries($space)->create($metadata + ['watched_on' => $date['watched_on'] ?? today()->toDateString()]);

                return to_route(static::ROUTE.'.show', [$space, $movie]);
            });
        } catch (UniqueConstraintViolationException $exception) {
            if ($tmdbId && ($existing = $this->entries($space)->where('tmdb_id', $tmdbId)->first())) {
                return to_route(static::ROUTE.'.show', [$space, $existing]);
            }

            throw $exception;
        }
    }

    public function update(Request $request, Space $space, int $entry)
    {
        $movie = $this->entries($space)->findOrFail($entry);

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'release_year' => 'sometimes|nullable|integer|between:1000,9999',
            'watched_on' => 'sometimes|required|date_format:Y-m-d|before_or_equal:today',
        ]);

        $title = $data['title'] ?? $movie->title;
        $year = array_key_exists('release_year', $data) ? $data['release_year'] : $movie->release_year;

        if ($movie->tmdb_id === null && $year === null && ($request->has('title') || $request->has('release_year'))
            && $this->entries($space)->whereKeyNot($movie->id)->pluck('title')->contains(fn ($other) => mb_strtolower($other) === mb_strtolower($title))) {
            throw ValidationException::withMessages(['release_year' => 'Indica el año de estreno para distinguir títulos iguales.']);
        }

        $movie->update($data);

        return to_route(static::ROUTE.'.show', [$space, $movie]);
    }

    public function rating(Request $request, Space $space, int $entry)
    {
        $movie = $this->entries($space)->findOrFail($entry);

        $data = Validator::make($request->all(), [
            'score' => 'present|nullable|integer|between:0,100',
            'note' => 'present|nullable|string|max:5000',
            'user_id' => 'prohibited',
        ])->validate();

        $movie->ratings()->upsert(
            [['user_id' => $request->user()->id] + $data],
            [static::FOREIGN_KEY, 'user_id'],
            ['score', 'note'],
        );

        return to_route(static::ROUTE.'.show', [$space, $movie]);
    }

    public function destroy(Request $request, Space $space, int $entry)
    {
        $movie = $this->entries($space)->findOrFail($entry);

        $request->validate(['confirmed' => 'accepted']);
        $movie->delete();

        return to_route(static::ROUTE.'.index', $space);
    }
}
