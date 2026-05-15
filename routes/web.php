<?php

use App\Http\Controllers\AsistenController;
use App\Http\Controllers\CompleteProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PraktikanAreaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD REDIRECT
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | LENGKAPI PROFIL PRAKTIKAN
    |--------------------------------------------------------------------------
    */
    Route::get('/lengkapi-profil', [CompleteProfileController::class, 'edit'])
        ->middleware('role:praktikan')
        ->name('profile.complete');

    Route::patch('/lengkapi-profil', [CompleteProfileController::class, 'update'])
        ->middleware('role:praktikan')
        ->name('profile.complete.update');

    /*
    |--------------------------------------------------------------------------
    | PRAKTIKAN
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:praktikan')->group(function () {
        Route::get('/dashboard/praktikan', [PraktikanAreaController::class, 'beranda'])
            ->name('dashboard.praktikan');

        Route::get('/praktikan/materi', [PraktikanAreaController::class, 'materi'])
            ->name('praktikan.materi');

        Route::get('/praktikan/jadwal', [PraktikanAreaController::class, 'jadwal'])
            ->name('praktikan.jadwal');

        Route::get('/praktikan/biodata', [PraktikanAreaController::class, 'biodata'])
            ->name('praktikan.biodata');

        Route::patch('/praktikan/biodata', [PraktikanAreaController::class, 'updateBiodata'])
            ->name('praktikan.biodata.update');

        Route::get('/praktikan/qr-code/download', [PraktikanAreaController::class, 'downloadQr'])
            ->name('praktikan.qr.download');
    });

    /*
    |--------------------------------------------------------------------------
    | ASISTEN
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:asisten')->group(function () {
        Route::get('/dashboard/asisten', [AsistenController::class, 'beranda'])
            ->name('dashboard.asisten');

        Route::get('/asisten/presensi', [AsistenController::class, 'presensi'])
            ->name('asisten.presensi');

        Route::post('/asisten/presensi/update', [AsistenController::class, 'updatePresensi'])
            ->name('asisten.presensi.update');

        Route::post('/asisten/presensi/scan', [AsistenController::class, 'scanQr'])
            ->name('asisten.presensi.scan');

        Route::post('/asisten/presensi/scan/store', [AsistenController::class, 'storeScanQr'])
            ->name('asisten.presensi.scan.store');

        Route::get('/asisten/materi', [AsistenController::class, 'materi'])
            ->name('asisten.materi');

        Route::get('/asisten/materi/{materi}/download', [AsistenController::class, 'downloadMateri'])
            ->name('asisten.materi.download');

        Route::get('/asisten/penilaian', [AsistenController::class, 'penilaian'])
            ->name('asisten.penilaian');

        Route::get('/asisten/penilaian/{praktikan}', [AsistenController::class, 'beriNilai'])
            ->name('asisten.penilaian.show');

        Route::post('/asisten/penilaian/{praktikan}', [AsistenController::class, 'simpanNilai'])
            ->name('asisten.penilaian.store');
    });

    /*
    |--------------------------------------------------------------------------
    | KOORDINATOR
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:koordinator')->group(function () {
        Route::get('/dashboard/koordinator', [DashboardController::class, 'koordinator'])
            ->name('dashboard.koordinator');
    });

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

require __DIR__ . '/auth.php';