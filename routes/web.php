<?php

use App\Http\Controllers\CompleteProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/lengkapi-profil', [CompleteProfileController::class, 'edit'])
        ->name('profile.complete');

    Route::patch('/lengkapi-profil', [CompleteProfileController::class, 'update'])
        ->name('profile.complete.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard/praktikan', [DashboardController::class, 'praktikan'])
        ->middleware('role:praktikan')
        ->name('dashboard.praktikan');

    Route::get('/dashboard/asisten', [DashboardController::class, 'asisten'])
        ->middleware('role:asisten')
        ->name('dashboard.asisten');

    Route::get('/dashboard/koordinator', [DashboardController::class, 'koordinator'])
        ->middleware('role:koordinator')
        ->name('dashboard.koordinator');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';