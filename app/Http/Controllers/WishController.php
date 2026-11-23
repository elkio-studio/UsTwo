<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\Wish;
use App\Rules\WishUrl;
use App\Support\WishPrice;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class WishController extends Controller
{
    public function index(Request $request, Space $space): Response
    {
        $lists = $space->wishLists()->with(['wishes' => fn ($query) => $query->with('author:id,username')->orderByDesc('id')])->orderBy('id')->get();
        $selected = $request->query('list');
        if ($selected !== null) abort_unless(is_string($selected) && ctype_digit($selected) && $lists->contains('id', (int) $selected), 404);
        return Inertia::render('Wishes/Wishes', [
            'spaceId' => $space->id,
            'spaceSlug' => $space->slug,
            'selectedListId' => $selected !== null ? (int) $selected : $lists->first()?->id,
            'lists' => $lists->map(fn ($list) => [
                'id' => $list->id, 'name' => $list->name,
                'wishes' => $list->wishes->map(fn ($wish) => [
                    'id' => $wish->id, 'url' => $wish->url, 'title' => $wish->title,
                    'description' => $wish->description, 'price' => WishPrice::decimal($wish->price_minor),
                    'currency' => $wish->currency, 'author' => $wish->author->username, 'status' => $wish->status,
                ]),
            ]),
        ]);
    }

    public function create(Space $space): Response
    {
        return Inertia::render('Wishes/Create', [
            'spaceId' => $space->id, 'spaceSlug' => $space->slug,
            'lists' => $space->wishLists()->orderBy('id')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request, Space $space)
    {
        $data = $this->validatedFields($request);
        try {
            DB::transaction(function () use ($request, $space, $data) {
                $locked = $this->lockSpace($request, $space);
                $list = $locked->wishLists()->whereKey($data['list_id'])->lockForUpdate()->first();
                if (! $list) throw ValidationException::withMessages(['list_id' => 'Elige una lista de esta estantería.']);
                $wish = $list->wishes()->make($this->editableFields($data));
                $wish->author_id = $request->user()->id;
                $wish->status = 'pending';
                $wish->save();
            }, 3);
        } catch (QueryException) {
            throw ValidationException::withMessages(['save' => 'No se pudo guardar el deseo. Conservamos los datos para que puedas reintentarlo.']);
        }
        return to_route('wishes.index', ['space' => $space->id, 'list' => $data['list_id']]);
    }

    public function edit(Space $space, int $wish): Response
    {
        $entry = $this->wishesIn($space)->findOrFail($wish);

        return Inertia::render('Wishes/Edit', [
            'spaceId' => $space->id,
            'lists' => $space->wishLists()->orderBy('id')->get(['id', 'name']),
            'wish' => [
                'id' => $entry->id, 'list_id' => $entry->wish_list_id,
                'url' => $entry->url, 'title' => $entry->title, 'description' => $entry->description,
                'price' => WishPrice::decimal($entry->price_minor),
            ],
        ]);
    }

    public function update(Request $request, Space $space, int $wish)
    {
        $this->wishesIn($space)->findOrFail($wish);
        $data = $this->validatedFields($request);
        try {
            DB::transaction(function () use ($request, $space, $wish, $data) {
                $locked = $this->lockSpace($request, $space);
                $entry = $this->wishesIn($locked)->lockForUpdate()->findOrFail($wish);
                $list = $locked->wishLists()->whereKey($data['list_id'])->lockForUpdate()->first();
                if (! $list) throw ValidationException::withMessages(['list_id' => 'Elige una lista de esta estantería.']);
                $entry->fill($this->editableFields($data));
                $entry->wish_list_id = $list->id;
                $entry->save();
            }, 3);
        } catch (QueryException) {
            throw ValidationException::withMessages(['save' => 'No se pudo guardar el deseo. Conservamos los datos para que puedas reintentarlo.']);
        }

        return to_route('wishes.index', ['space' => $space->id, 'list' => $data['list_id']]);
    }

    public function destroy(Request $request, Space $space, int $wish)
    {
        $this->wishesIn($space)->findOrFail($wish);
        $request->validate(['confirmed' => 'required|accepted']);
        try {
            $listId = DB::transaction(function () use ($request, $space, $wish) {
                $locked = $this->lockSpace($request, $space);
                $entry = $this->wishesIn($locked)->lockForUpdate()->findOrFail($wish);
                $listId = $entry->wish_list_id;
                $entry->delete();

                return $listId;
            }, 3);
        } catch (QueryException) {
            throw ValidationException::withMessages(['delete' => 'No se pudo eliminar el deseo. Sigue guardado; puedes reintentarlo.']);
        }

        return to_route('wishes.index', ['space' => $space->id, 'list' => $listId]);
    }

    private function wishesIn(Space $space)
    {
        return Wish::whereIn('wish_list_id', $space->wishLists()->select('id'));
    }

    private function lockSpace(Request $request, Space $space): Space
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::table('spaces')->where('id', $space->id)->update(['id' => DB::raw('id')]);
        }
        $locked = Space::whereKey($space->id)->lockForUpdate()->firstOrFail();
        abort_unless($locked->members()->whereKey($request->user()->id)->exists(), 404);

        return $locked;
    }

    private function editableFields(array $data): array
    {
        return [
            'url' => $data['url'], 'title' => $data['title'], 'description' => $data['description'] ?? null,
            'price_minor' => isset($data['price']) ? WishPrice::minor($data['price']) : null,
            'currency' => isset($data['price']) ? 'EUR' : null,
        ];
    }

    private function validatedFields(Request $request): array
    {
        return $request->validate([
            'list_id' => 'required|integer|min:1', 'url' => ['required', 'string', new WishUrl],
            'title' => 'required|string|max:255', 'description' => 'nullable|string|max:5000',
            'price' => ['nullable', function ($attribute, $value, $fail) {
                if (WishPrice::minor($value) === null) $fail('Indica un precio entre 0 y 99999999,99, con hasta dos decimales.');
            }],
            'currency' => ['nullable', Rule::prohibitedIf(! $request->filled('price')), Rule::in(WishPrice::CURRENCIES)],
        ]);
    }
}
