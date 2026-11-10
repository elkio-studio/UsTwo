<?php

namespace App\Http\Middleware;

use App\Models\SpaceInvitation;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return parent::share($request) + [
            'sessionUsername' => fn () => $request->user()?->username,
            'invitations' => fn () => $request->user() ? SpaceInvitation::where('user_id', $request->user()->id)
                ->where('status', 'pending')->join('spaces', 'spaces.id', '=', 'space_invitations.space_id')
                ->join('users as inviters', 'inviters.id', '=', 'spaces.owner_id')
                ->orderBy('space_invitations.id')
                ->get(['space_invitations.id', 'spaces.name as space', 'inviters.username as inviter'])
                ->map->only(['id', 'space', 'inviter']) : [],
        ];
    }
}
