<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('StackCheck', [
        'message' => 'Laravel e Inertia están conectados con React.',
    ]);
})->name('stack-check');
