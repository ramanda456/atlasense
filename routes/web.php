<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtentikasiController;
use App\Http\Controllers\DasborController;
use App\Http\Controllers\NegaraController;
use App\Http\Controllers\PantauCuacaController;
use App\Http\Controllers\PantauKursController;
use App\Http\Controllers\PantauBeritaController;
use App\Http\Controllers\PantauPelabuhanController;
use App\Http\Controllers\AnalisisRisikoController;
use App\Http\Controllers\PerbandinganController;
use App\Http\Controllers\VisualisasiController;
use App\Http\Controllers\PemantauanController;
use App\Http\Controllers\Admin\DasborAdminController;
use App\Http\Controllers\Admin\KelolaPenggunaController;
use App\Http\Controllers\Admin\KelolaPelabuhanController;
use App\Http\Controllers\Admin\KelolaArtikelController;
use App\Http\Controllers\Admin\KelolaKataController;
use App\Http\Controllers\Admin\KelolaLogController;

/**
 * =====================================================================
 * Web Routes — AtlaSense
 * =====================================================================
 */

// ======== ROUTE AUTENTIKASI (PUBLIK) ========
Route::get('/login', [OtentikasiController::class, 'showLogin'])->name('login');
Route::post('/login', [OtentikasiController::class, 'login']);
Route::get('/register', [OtentikasiController::class, 'showRegister'])->name('register');
Route::post('/register', [OtentikasiController::class, 'register']);
Route::post('/logout', [OtentikasiController::class, 'logout'])->name('logout');

// ======== ROUTE TERPROTEKSI MIDDLEWARE AUTH ========
Route::middleware('auth')->group(function () {

    // Dashboard Utama & AJAX Live Update
    Route::get('/', [DasborController::class, 'index'])->name('dashboard');
    Route::get('/dasbor/live', [DasborController::class, 'live'])->name('dashboard.live');

    // Watchlist Actions (Daftar Pantauan)
    Route::get('/pemantauan', [PemantauanController::class, 'index'])->name('watchlists.index');
    Route::post('/pemantauan/toggle', [PemantauanController::class, 'toggle'])->name('watchlist.toggle');

    // Intelijen Global — Halaman Negara & Detail
    Route::get('/daftar-negara', [NegaraController::class, 'index'])->name('countries.index');
    Route::get('/daftar-negara/{code}', [NegaraController::class, 'show'])->name('countries.show');
    Route::get('/perbandingan', [PerbandinganController::class, 'index'])->name('countries.compare');

    // Intelijen Global — Analisis & Visualisasi
    Route::get('/analisis-risiko', [AnalisisRisikoController::class, 'index'])->name('analysis.risk');
    Route::post('/analisis-risiko/{code}/hitung', [AnalisisRisikoController::class, 'hitung'])->name('analysis.calculate');
    Route::get('/visualisasi', [VisualisasiController::class, 'index'])->name('analysis.visualization');

    // Monitoring Dashboards
    Route::get('/pantau-cuaca', [PantauCuacaController::class, 'index'])->name('monitoring.weather');
    Route::get('/pantau-kurs', [PantauKursController::class, 'index'])->name('monitoring.currency');
    Route::get('/pantau-kurs/chart-data', [PantauKursController::class, 'chartData'])->name('monitoring.currency.chart');
    Route::get('/pantau-pelabuhan', [PantauPelabuhanController::class, 'index'])->name('monitoring.ports');
    Route::get('/pantau-berita', [PantauBeritaController::class, 'index'])->name('monitoring.news');

    // ======== ROUTE KHUSUS ADMINISTRATOR ========
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [DasborAdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/admin/settings', [DasborAdminController::class, 'updateSettings'])->name('admin.settings.update');
        
        // User management
        Route::get('/admin/users', [KelolaPenggunaController::class, 'index'])->name('admin.users');
        Route::post('/admin/users', [KelolaPenggunaController::class, 'store'])->name('admin.users.create');
        Route::delete('/admin/users/{id}', [KelolaPenggunaController::class, 'destroy'])->name('admin.users.delete');

        // Port management
        Route::get('/admin/ports', [KelolaPelabuhanController::class, 'index'])->name('admin.ports');
        Route::post('/admin/ports', [KelolaPelabuhanController::class, 'store'])->name('admin.ports.create');
        Route::post('/admin/ports/import', [KelolaPelabuhanController::class, 'import'])->name('admin.ports.import');
        Route::delete('/admin/ports/{id}', [KelolaPelabuhanController::class, 'destroy'])->name('admin.ports.delete');

        // Lexicon dictionary management
        Route::get('/admin/words', [KelolaKataController::class, 'index'])->name('admin.words');
        Route::post('/admin/words', [KelolaKataController::class, 'store'])->name('admin.words.add');
        Route::delete('/admin/words/{type}/{id}', [KelolaKataController::class, 'destroy'])->name('admin.words.delete');

        // Article management
        Route::get('/admin/articles', [KelolaArtikelController::class, 'index'])->name('admin.articles');
        Route::post('/admin/articles', [KelolaArtikelController::class, 'store'])->name('admin.articles.create');
        Route::put('/admin/articles/{id}', [KelolaArtikelController::class, 'update'])->name('admin.articles.update');
        Route::delete('/admin/articles/{id}', [KelolaArtikelController::class, 'destroy'])->name('admin.articles.delete');

        // API logs checking
        Route::get('/admin/api-logs', [KelolaLogController::class, 'index'])->name('admin.api-logs');
        Route::delete('/admin/api-logs/clear', [KelolaLogController::class, 'clear'])->name('admin.api-logs.clear');
    });
});
