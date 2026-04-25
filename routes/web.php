<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;

Route::get('/redirect', function () {
    $role = Auth::user()->role;

    if ($role == 'admin') {
        return view('admin.dashboard');
    } elseif ($role == 'reviewer') {
        return view('reviewer.dashboard');
    } else {
        return view('researcher.dashboard');
    }
})->middleware(['auth']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
