<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
    Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
    Route::get('{provider}/redirect', [\App\Http\Controllers\AuthController::class, 'redirect'])->name('auth.redirect');
    Route::get('{provider}/callback', [\App\Http\Controllers\AuthController::class, 'callback'])->name('auth.callback');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', \App\Livewire\Dashboard::class)->name('dashboard');
});
