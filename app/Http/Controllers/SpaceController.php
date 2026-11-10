<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\SpaceInvitation;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class SpaceController extends Controller
{
    public function index(Request $request)
    {
        return $this->dashboard($request);
    }

    public function show(Request $request, string $shelf)
    {
        $space = $request->user()->spaces()->where('spaces.slug', $shelf)->firstOrFail();
        $request->session()->put('active_space', $space->id);

        return $this->dashboard($request, $space);
    }

    private function dashboard(Request $request, ?Space $active = null)
    {
        $spaces = $request->user()->spaces()->orderBy('spaces.id')->get(['spaces.id', 'name', 'slug']);
        $isOwner = $active && $active->owner_id === $request->user()->id;

        return Inertia::render('Dashboard/Dashboard', [
            'spaces' => $spaces->map->only(['id', 'name', 'slug']),
            'activeSpace' => $active?->only(['id', 'name', 'slug', 'movie_slot', 'series_slot']),
            'isOwner' => (bool) $isOwner,
            'editing' => $isOwner && $request->boolean('editar'),
            'members' => $isOwner ? $active->members()->whereKeyNot($active->owner_id)->orderBy('users.id')
                ->get(['users.id', 'username'])->map->only(['id', 'username']) : [],
            'outgoing' => $isOwner ? $active->invitations()->where('status', 'pending')->with('recipient:id,username')
                ->get()->map(fn ($invitation) => ['id' => $invitation->id, 'username' => $invitation->recipient->username]) : [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        $space = retry(3, fn () => DB::transaction(function () use ($request, $data) {
            $space = Space::create($data + ['owner_id' => $request->user()->id]);
            $space->members()->attach($request->user()->id);

            return $space;
        }, 3), 0, fn ($error) => $error instanceof UniqueConstraintViolationException);

        return $this->select($request, $space);
    }

    public function select(Request $request, Space $space)
    {
        $request->session()->put('active_space', $space->id);

        return to_route('spaces.show', $space->slug);
    }

    public function destroy(Request $request, Space $space)
    {
        abort_unless($space->owner_id === $request->user()->id, 403);
        $request->validate(['confirmed' => 'accepted']);
        $space->delete();

        return to_route('dashboard');
    }

    public function rename(Request $request, Space $space)
    {
        abort_unless($space->owner_id === $request->user()->id, 403);
        $data = $request->validate(['name' => 'required|string|max:100']);

        if ($space->name !== $data['name']) {
            retry(3, function () use ($space, $data) {
                $space->slug = Space::availableSlug($data['name'], $space->id);
                $space->update($data);
            }, 0, fn ($error) => $error instanceof UniqueConstraintViolationException);
        }

        return to_route('spaces.show', $space->slug);
    }

    public function placeMovieDrawer(Request $request, Space $space)
    {
        return $this->placeDrawer($request, $space, 'movie_slot', 'series_slot');
    }

    public function placeSeriesDrawer(Request $request, Space $space)
    {
        return $this->placeDrawer($request, $space, 'series_slot', 'movie_slot');
    }

    private function placeDrawer(Request $request, Space $space, string $column, string $other)
    {
        $data = $request->validate(['slot' => 'required|integer|between:1,8']);
        $slot = (int) $data['slot'];
        $placed = Space::whereKey($space->id)
            ->where(fn ($query) => $query->whereNull($other)->orWhere($other, '!=', $slot))
            ->update([$column => $slot]);

        if (! $placed) {
            throw ValidationException::withMessages(['slot' => 'Este hueco ya está ocupado. Elige otro.']);
        }

        return to_route('spaces.show', $space->slug);
    }

    public function removeMember(Request $request, Space $space, string $member)
    {
        abort_unless($space->owner_id === $request->user()->id, 403);
        abort_if($member === (string) $space->owner_id, 403);
        $space->members()->wherePivot('user_id', '!=', $space->owner_id)->detach($member);

        return to_route('spaces.show', $space->slug);
    }

    public function leave(Request $request, Space $space)
    {
        abort_if($space->owner_id === $request->user()->id, 403);
        $space->members()->detach($request->user()->id);

        if ($request->session()->get('active_space') === $space->id) {
            $request->session()->forget('active_space');
        }

        return to_route($request->boolean('from_account') ? 'account.show' : 'dashboard');
    }

    public function invite(Request $request, Space $space)
    {
        abort_unless($space->owner_id === $request->user()->id, 403);
        $data = $request->validate(['username' => ['required', 'string', User::USERNAME_PATTERN]]);
        $recipient = User::where('username_key', strtolower($data['username']))->first();

        DB::transaction(function () use ($space, $recipient) {
            $lockedSpace = Space::whereKey($space->id)->lockForUpdate()->firstOrFail();
            if (! $recipient || $lockedSpace->members()->whereKey($recipient->id)->exists()) {
                throw ValidationException::withMessages(['username' => 'No se puede invitar a ese nombre de usuario.']);
            }

            $lockedSpace->invitations()->where('user_id', $recipient->id)->where('status', '!=', 'pending')->delete();
            $lockedSpace->invitations()->firstOrCreate(['user_id' => $recipient->id], ['status' => 'pending']);
        }, 3);

        return back();
    }

    public function withdraw(Request $request, Space $space, SpaceInvitation $invitation)
    {
        abort_unless($space->owner_id === $request->user()->id, 403);
        $space->invitations()->whereKey($invitation->id)->where('status', 'pending')->update(['status' => 'withdrawn']);

        return back();
    }

    public function respond(Request $request, string $invitation)
    {
        $data = $request->validate(['response' => 'required|in:accepted,rejected']);
        $pending = SpaceInvitation::where('user_id', $request->user()->id)->find($invitation);
        if (! $pending) {
            throw ValidationException::withMessages(['invitation' => 'Esta invitación ya no está disponible.']);
        }

        $handled = DB::transaction(function () use ($pending, $data) {
            $space = Space::whereKey($pending->space_id)->lockForUpdate()->first();
            if (! $space) {
                return false;
            }

            $invitation = $space->invitations()->whereKey($pending->id);
            $changed = (clone $invitation)->where('status', 'pending')->update(['status' => $data['response']]);
            if ($changed && $data['response'] === 'accepted') {
                DB::table('space_user')->insertOrIgnore(['space_id' => $space->id, 'user_id' => $pending->user_id]);
            }

            return $changed || $invitation->where('status', $data['response'])->exists();
        }, 3);

        if (! $handled) {
            throw ValidationException::withMessages(['invitation' => 'Esta invitación ya no está disponible.']);
        }

        return back();
    }
}
