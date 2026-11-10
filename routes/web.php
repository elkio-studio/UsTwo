<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/acceso', fn () => Inertia::render('Login/Login'))->name('login');
    Route::post('/acceso', [SessionController::class, 'store'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => Inertia::render('Dashboard/Dashboard'))->name('dashboard');
    Route::post('/salir', [SessionController::class, 'destroy']);
    Route::get('/creditos', fn () => Inertia::render('Credits/Credits'));
    Route::get('/peliculas', [MovieController::class, 'index'])->name('movies.index');
    Route::get('/peliculas/nueva', [MovieController::class, 'create'])->name('movies.create');
    Route::get('/peliculas/catalogo', [MovieController::class, 'search'])->middleware('throttle:60,1');
    Route::post('/peliculas', [MovieController::class, 'store']);
    Route::get('/peliculas/{movie}', [MovieController::class, 'show'])->whereNumber('movie')->name('movies.show');
    Route::patch('/peliculas/{movie}', [MovieController::class, 'update'])->whereNumber('movie');
    Route::put('/peliculas/{movie}/valoracion', [MovieController::class, 'rating'])->whereNumber('movie');
    Route::delete('/peliculas/{movie}', [MovieController::class, 'destroy'])->whereNumber('movie');
});
