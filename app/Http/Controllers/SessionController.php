<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class SessionController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', User::USERNAME_PATTERN],
            'email' => 'required|email|max:255',
            'password' => ['required', 'string', 'min:12', function ($attribute, $value, $fail) {
                if (is_string($value) && strlen($value) > 72) {
                    $fail('La contraseña debe ocupar como máximo 72 bytes.');
                }
            }],
        ]);
        $data['email'] = mb_strtolower($data['email']);
        $data['username_key'] = strtolower($data['username']);

        try {
            $user = DB::transaction(fn () => User::create($data));
        } catch (UniqueConstraintViolationException $exception) {
            $errors = [];
            if (User::where('email', $data['email'])->exists()) {
                $errors['email'] = 'Este correo ya está en uso.';
            }
            if (User::where('username_key', $data['username_key'])->exists()) {
                $errors['username'] = 'Este nombre de usuario ya está en uso.';
            }
            if ($errors === []) {
                throw $exception;
            }
            throw ValidationException::withMessages($errors);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return to_route('dashboard');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:72',
        ]);
        $credentials['email'] = mb_strtolower($credentials['email']);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'El correo o la contraseña no coinciden.']);
        }

        $request->session()->regenerate();

        $request->session()->forget(['active_space', 'url.intended']);

        return to_route('dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Inertia::clearHistory();

        return redirect()->route('login');
    }
}
