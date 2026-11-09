<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\User;
use App\Services\Tmdb;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MovieController extends Controller
{
    public function index()
    {
        return Inertia::render('Movies/Movies', [
            'movies' => Movie::with('ratings:id,movie_id,user_id,score')
                ->orderByDesc('watched_on')->orderByDesc('id')->get(['id', 'title']),
            'people' => User::orderBy('id')->get(['id', 'name']),
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('MovieCreate/MovieCreate', ['candidates' => $request->session()->get('movie_candidates', [])]);
    }

    public function search(Request $request, Tmdb $tmdb)
    {
        $query = $request->validate(['q' => 'required|string|max:255']);

        return response()->json(['movies' => $tmdb->search($query['q'])]);
    }

    public function show(Movie $movie, Request $request)
    {
        return Inertia::render('MovieDetail/MovieDetail', [
            'movie' => $movie->load('ratings'),
            'people' => User::orderBy('id')->get(['id', 'name']),
            'viewerId' => $request->user()->id,
        ]);
    }

    public function store(Request $request, Tmdb $tmdb)
    {
        $input = $request->validate([
            'tmdb_id' => 'nullable|integer|min:1',
            'candidate_id' => 'nullable|integer|min:1',
            'distinct' => 'sometimes|boolean',
            'candidate_ids' => 'sometimes|array',
            'candidate_ids.*' => 'integer|min:1',
        ]);
        $tmdbId = $input['tmdb_id'] ?? null;

        if ($tmdbId && ($existing = Movie::where('tmdb_id', $tmdbId)->first())) {
            return to_route('movies.show', $existing);
        }

        $metadata = $tmdbId
            ? $tmdb->find($tmdbId)
            : $request->validate([
                'title' => 'required|string|max:255',
                'release_year' => 'nullable|integer|between:1000,9999',
            ]);
        $year = $metadata['release_year'] ?? null;

        try {
            return DB::transaction(function () use ($request, $input, $tmdbId, $metadata, $year) {
                $sameTitle = Movie::when($tmdbId, fn ($query) => $query->whereNull('tmdb_id'))
                    ->orderBy('id')->get(['id', 'title', 'release_year', 'watched_on'])
                    ->filter(fn (Movie $movie) => mb_strtolower($movie->title) === mb_strtolower($metadata['title']));

                if (! $tmdbId && $year === null && $sameTitle->isNotEmpty()) {
                    throw ValidationException::withMessages(['release_year' => 'Indica el año de estreno para distinguir títulos iguales.']);
                }

                $candidates = $sameTitle->filter(fn (Movie $movie) => $year === null
                    || $movie->release_year === null || (int) $movie->release_year === (int) $year)->values();

                if ($candidateId = $input['candidate_id'] ?? null) {
                    $candidate = $candidates->firstWhere('id', $candidateId);
                    if ($candidate && (! $tmdbId || Movie::whereKey($candidateId)->whereNull('tmdb_id')->update(['tmdb_id' => $tmdbId]))) {
                        return to_route('movies.show', $candidate);
                    }
                }

                $reviewedIds = collect($input['candidate_ids'] ?? [])->map(fn ($id) => (int) $id)->sort()->values()->all();
                if ($candidates->isNotEmpty() && (! ($input['distinct'] ?? false)
                    || $reviewedIds !== $candidates->pluck('id')->sort()->values()->all())) {
                    return back()->with('movie_candidates', $candidates)
                        ->withErrors(['match' => 'Confirma si alguna entrada corresponde a la misma película.']);
                }

                if (! empty($input['candidate_id'])) {
                    throw ValidationException::withMessages(['match' => 'La entrada elegida ya no está disponible. Revisa la selección.']);
                }

                $date = $request->validate(['watched_on' => 'required|date_format:Y-m-d|before_or_equal:today']);
                $movie = Movie::create($metadata + $date);

                return to_route('movies.show', $movie);
            });
        } catch (UniqueConstraintViolationException $exception) {
            if ($tmdbId && ($existing = Movie::where('tmdb_id', $tmdbId)->first())) {
                return to_route('movies.show', $existing);
            }

            throw $exception;
        }
    }

    public function update(Request $request, Movie $movie)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'release_year' => 'sometimes|nullable|integer|between:1000,9999',
            'watched_on' => 'sometimes|required|date_format:Y-m-d|before_or_equal:today',
        ]);

        $title = $data['title'] ?? $movie->title;
        $year = array_key_exists('release_year', $data) ? $data['release_year'] : $movie->release_year;

        if ($movie->tmdb_id === null && $year === null && ($request->has('title') || $request->has('release_year'))
            && Movie::whereKeyNot($movie->id)->pluck('title')->contains(fn ($other) => mb_strtolower($other) === mb_strtolower($title))) {
            throw ValidationException::withMessages(['release_year' => 'Indica el año de estreno para distinguir títulos iguales.']);
        }

        $movie->update($data);

        return to_route('movies.show', $movie);
    }

    public function rating(Request $request, Movie $movie)
    {
        $data = Validator::make($request->all(), [
            'score' => 'present|nullable|integer|between:0,100',
            'note' => 'present|nullable|string|max:5000',
            'user_id' => 'prohibited',
        ])->validate();

        $movie->ratings()->upsert(
            [['user_id' => $request->user()->id] + $data],
            ['movie_id', 'user_id'],
            ['score', 'note'],
        );

        return to_route('movies.show', $movie);
    }

    public function destroy(Request $request, Movie $movie)
    {
        $request->validate(['confirmed' => 'accepted']);
        $movie->delete();

        return to_route('movies.index');
    }
}
