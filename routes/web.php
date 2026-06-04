<?php

use App\Http\Controllers\AsistenController;
use App\Http\Controllers\CompleteProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PraktikanAreaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KoordinatorController;
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

        Route::get('/praktikan/materi/{materi}/download', [PraktikanAreaController::class, 'downloadMateri'])
            ->name('praktikan.materi.download');

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
        Route::get('/dashboard/koordinator', [KoordinatorController::class, 'beranda'])
            ->name('dashboard.koordinator');

        Route::get('/koordinator/periode', [KoordinatorController::class, 'periode'])
            ->name('koordinator.periode');

        Route::post('/koordinator/periode', [KoordinatorController::class, 'storePeriode'])
            ->name('koordinator.periode.store');

        Route::get('/koordinator/periode/{praktikum}', [KoordinatorController::class, 'periodeDetail'])
            ->name('koordinator.periode.show');

        Route::patch('/koordinator/periode/{praktikum}', [KoordinatorController::class, 'updatePeriode'])
            ->name('koordinator.periode.update');

        Route::get('/koordinator/materi', [KoordinatorController::class, 'materi'])
            ->name('koordinator.materi');

        Route::post('/koordinator/materi', [KoordinatorController::class, 'storeMateri'])
            ->name('koordinator.materi.store');

        Route::delete('/koordinator/materi/{materi}', [KoordinatorController::class, 'deleteMateri'])
            ->name('koordinator.materi.delete');

        Route::get('/koordinator/asisten', [KoordinatorController::class, 'asisten'])
            ->name('koordinator.asisten');

        Route::post('/koordinator/asisten', [KoordinatorController::class, 'storeAsisten'])
            ->name('koordinator.asisten.store');

        Route::patch('/koordinator/asisten/{asisten}/reset-password', [KoordinatorController::class, 'resetPasswordAsisten'])
            ->name('koordinator.asisten.reset-password');

        Route::get('/koordinator/praktikan', [KoordinatorController::class, 'praktikan'])
            ->name('koordinator.praktikan');

        Route::get('/koordinator/kelompok', [KoordinatorController::class, 'kelompok'])
            ->name('koordinator.kelompok');

        Route::post('/koordinator/kelompok', [KoordinatorController::class, 'storeKelompok'])
            ->name('koordinator.kelompok.store');

        Route::get('/koordinator/kelompok/{kelompok}', [KoordinatorController::class, 'kelompokDetail'])
            ->name('koordinator.kelompok.show');

        Route::patch('/koordinator/kelompok/{kelompok}', [KoordinatorController::class, 'updateKelompok'])
            ->name('koordinator.kelompok.update');

        Route::get('/koordinator/penilaian', [KoordinatorController::class, 'penilaian'])
            ->name('koordinator.penilaian');

        Route::get('/koordinator/presensi', [KoordinatorController::class, 'presensi'])
            ->name('koordinator.presensi');

        Route::get('/koordinator/nilai-akhir', [KoordinatorController::class, 'nilaiAkhir'])
            ->name('koordinator.nilai-akhir');

        Route::get('/koordinator/nilai-akhir/export', [KoordinatorController::class, 'exportNilaiAkhir'])
            ->name('koordinator.nilai-akhir.export');

        Route::get('/koordinator/evaluasi-algoritma', [KoordinatorController::class, 'evaluasiAlgoritma'])
            ->name('koordinator.evaluasi-algoritma');
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