<?php

use App\Http\Controllers\CompleteProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PraktikanAreaController;
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

    /*
    |--------------------------------------------------------------------------
    | PRAKTIKAN
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard/praktikan', [PraktikanAreaController::class, 'beranda'])
        ->middleware('role:praktikan')
        ->name('dashboard.praktikan');

    Route::get('/praktikan/materi', [PraktikanAreaController::class, 'materi'])
        ->middleware('role:praktikan')
        ->name('praktikan.materi');

    Route::get('/praktikan/jadwal', [PraktikanAreaController::class, 'jadwal'])
        ->middleware('role:praktikan')
        ->name('praktikan.jadwal');

    Route::get('/praktikan/biodata', [PraktikanAreaController::class, 'biodata'])
        ->middleware('role:praktikan')
        ->name('praktikan.biodata');

    Route::patch('/praktikan/biodata', [PraktikanAreaController::class, 'updateBiodata'])
        ->middleware('role:praktikan')
        ->name('praktikan.biodata.update');

    Route::get('/praktikan/qr-code/download', [PraktikanAreaController::class, 'downloadQr'])
        ->middleware('role:praktikan')
        ->name('praktikan.qr.download');

    /*
    |--------------------------------------------------------------------------
    | ASISTEN
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard/asisten', [DashboardController::class, 'asisten'])
        ->middleware('role:asisten')
        ->name('dashboard.asisten');

    /*
    |--------------------------------------------------------------------------
    | KOORDINATOR
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard/koordinator', [DashboardController::class, 'koordinator'])
        ->middleware('role:koordinator')
        ->name('dashboard.koordinator');

    /*
    |--------------------------------------------------------------------------
    | PROFILE BREEZE
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';