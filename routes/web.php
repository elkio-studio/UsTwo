<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SpaceController;
use App\Http\Middleware\RequireSpaceMembership;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/acceso', fn () => Inertia::render('Login/Login'))->name('login');
    Route::post('/acceso', [SessionController::class, 'store'])->middleware('throttle:10,1');
    Route::post('/registro', [SessionController::class, 'register'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [SpaceController::class, 'index'])->name('dashboard');
    Route::post('/salir', [SessionController::class, 'destroy']);
    Route::get('/mi/cuenta', [AccountController::class, 'show'])->name('account.show');
    Route::patch('/mi/cuenta', [AccountController::class, 'update'])->middleware('throttle:10,1');
    Route::get('/creditos', fn () => Inertia::render('Credits/Credits'));
    Route::get('/peliculas/{legacy?}', [MovieController::class, 'legacy'])->where('legacy', 'nueva|[0-9]+');

    Route::post('/espacios', [SpaceController::class, 'store']);
    Route::patch('/invitaciones/{invitation}', [SpaceController::class, 'respond'])->whereNumber('invitation');

    Route::prefix('/espacios/{space}')->where(['space' => '[0-9]+'])->scopeBindings()->middleware(RequireSpaceMembership::class)->group(function () {
        Route::put('/activo', [SpaceController::class, 'select']);
        Route::put('/cajones/peliculas', [SpaceController::class, 'placeMovieDrawer']);
        Route::delete('/pertenencia', [SpaceController::class, 'leave']);
        Route::patch('/', [SpaceController::class, 'rename']);
        Route::delete('/miembros/{member}', [SpaceController::class, 'removeMember'])->whereNumber('member');
        Route::delete('/', [SpaceController::class, 'destroy']);
        Route::post('/invitaciones', [SpaceController::class, 'invite']);
        Route::delete('/invitaciones/{invitation}', [SpaceController::class, 'withdraw'])->whereNumber('invitation');

        Route::get('/peliculas', [MovieController::class, 'index'])->name('movies.index');
        Route::get('/peliculas/nueva', [MovieController::class, 'create'])->name('movies.create');
        Route::get('/peliculas/catalogo', [MovieController::class, 'search'])->middleware('throttle:60,1');
        Route::post('/peliculas', [MovieController::class, 'store']);
        Route::get('/peliculas/{movie}', [MovieController::class, 'show'])->whereNumber('movie')->name('movies.show');
        Route::patch('/peliculas/{movie}', [MovieController::class, 'update'])->whereNumber('movie');
        Route::put('/peliculas/{movie}/valoracion', [MovieController::class, 'rating'])->whereNumber('movie');
        Route::delete('/peliculas/{movie}', [MovieController::class, 'destroy'])->whereNumber('movie');
    });

    Route::get('/{shelf}', [SpaceController::class, 'show'])->where('shelf', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('spaces.show');
});
