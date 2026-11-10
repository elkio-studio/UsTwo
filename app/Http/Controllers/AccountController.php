<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $spaces = $user->spaces()->orderBy('name')->get(['spaces.id', 'name', 'slug', 'owner_id'])
            ->map(fn ($space) => $space->only(['id', 'name', 'slug']) + ['isOwner' => $space->owner_id === $user->id]);

        return Inertia::render('Account/Account', ['spaces' => $spaces]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $request->merge(['username_key' => is_string($request->input('username')) ? strtolower($request->input('username')) : null]);
        $data = $request->validate([
            'username' => ['required', 'string', User::USERNAME_PATTERN],
            'username_key' => [Rule::unique('users')->ignore($user->id)],
            'current_password' => ['required', 'string', 'max:72', 'current_password'],
            'password' => ['nullable', 'string', 'min:12', 'confirmed', function ($attribute, $value, $fail) {
                if (is_string($value) && strlen($value) > 72) {
                    $fail('La contraseña debe ocupar como máximo 72 bytes.');
                }
            }],
        ], [
            'username_key.unique' => 'Este nombre de usuario ya está en uso.',
            'current_password.current_password' => 'La contraseña actual no coincide.',
        ]);

        unset($data['current_password']);
        if (empty($data['password'])) {
            unset($data['password']);
        }

        try {
            DB::transaction(fn () => $user->update($data));
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['username_key' => 'Este nombre de usuario ya está en uso.']);
        }

        if (isset($data['password'])) {
            $request->session()->regenerate(true);
        }

        return to_route('account.show');
    }
}
