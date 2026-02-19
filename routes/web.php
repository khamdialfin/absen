<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\BendaharaController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════════
// PUBLIC ROUTES
// ══════════════════════════════════════════════════════════════════

Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'showLogin'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.post')
    ->middleware('guest');

Route::get('/register', [LoginController::class, 'showRegister'])
    ->name('register')
    ->middleware('guest');

// ══════════════════════════════════════════════════════════════════
// GOOGLE OAUTH
// ══════════════════════════════════════════════════════════════════

Route::get('/auth/google', [SocialiteController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/admin/auth/google/callback', [SocialiteController::class, 'callback'])
    ->name('google.callback');

use App\Http\Controllers\Auth\ProfileSetupController;

// ══════════════════════════════════════════════════════════════════
// AUTH ROUTES (sudah login, tapi mungkin belum lengkap)
// ══════════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {

    // Step 1: Set Password (wajib setelah Google login pertama kali)
    Route::get('/password/setup', [SetPasswordController::class, 'show'])
        ->name('password.setup');
    Route::post('/password/setup', [SetPasswordController::class, 'store'])
        ->name('password.store');

    // Step 2: Setup Profil (wajib setelah set password)
    Route::get('/profile/setup', [ProfileSetupController::class, 'show'])
        ->name('profile.setup');
    Route::post('/profile/setup', [ProfileSetupController::class, 'store'])
        ->name('profile.store');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});

// ══════════════════════════════════════════════════════════════════
// PROTECTED ROUTES (login + password sudah di-set)
// ══════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'password.set'])->group(function () {

    // Dashboard (redirect berdasarkan role)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ── WALIKELAS ────────────────────────────────────────────────
    Route::middleware('role:walikelas')
        ->prefix('walikelas')
        ->name('walikelas.')
        ->group(function () {
            Route::get('/rekap', [RekapController::class, 'index'])
                ->name('rekap');
            Route::get('/rekap/export', [RekapController::class, 'export'])
                ->name('rekap.export');
        });

    // ── SEKERTARIS ───────────────────────────────────────────────
    Route::middleware('role:sekertaris')
        ->prefix('sekertaris')
        ->name('sekertaris.')
        ->group(function () {
            Route::get('/scan', [AttendanceController::class, 'scanPage'])
                ->name('scan');
            Route::post('/attendance/scan', [AttendanceController::class, 'processScan'])
                ->name('attendance.scan');
            Route::post('/session/start', [AttendanceController::class, 'startSession'])
                ->name('session.start');
            Route::post('/session/stop', [AttendanceController::class, 'stopSession'])
                ->name('session.stop');
            Route::get('/attendance/manual', [AttendanceController::class, 'manualForm'])
                ->name('attendance.manual');
            Route::post('/attendance/manual', [AttendanceController::class, 'storeManual'])
                ->name('attendance.store-manual');
            Route::get('/recap', [AttendanceController::class, 'recap'])
                ->name('recap');
            Route::get('/recap/export', [AttendanceController::class, 'exportRecap'])
                ->name('recap.export');

            // Laporan Tambahan
            Route::get('/recap/masuk', [AttendanceController::class, 'recapMasuk'])
                ->name('recap.masuk');
            Route::get('/recap/pulang', [AttendanceController::class, 'recapPulang'])
                ->name('recap.pulang');
            Route::get('/report/combined', [AttendanceController::class, 'reportCombined'])
                ->name('report.combined');
        });

    // ── SISWA ────────────────────────────────────────────────────
    Route::middleware('role:siswa')
        ->prefix('siswa')
        ->name('siswa.')
        ->group(function () {
            Route::get('/qr', [AttendanceController::class, 'generateQr'])
                ->name('qr');
            Route::post('/qr/generate', [AttendanceController::class, 'generateQrData'])
                ->name('qr.generate');
            Route::get('/riwayat', [AttendanceController::class, 'riwayat'])
                ->name('riwayat');
        });

    // ── BENDAHARA ────────────────────────────────────────────────
    Route::middleware('role:bendahara')
        ->prefix('bendahara')
        ->name('bendahara.')
        ->group(function () {
            Route::get('/dashboard', [BendaharaController::class, 'index'])->name('dashboard');
            
            Route::get('/pemasukan', [BendaharaController::class, 'pemasukan'])->name('pemasukan');
            Route::post('/pemasukan', [BendaharaController::class, 'storePemasukan'])->name('pemasukan.store');
            
            Route::get('/pengeluaran', [BendaharaController::class, 'pengeluaran'])->name('pengeluaran');
            Route::post('/pengeluaran', [BendaharaController::class, 'storePengeluaran'])->name('pengeluaran.store');
            
            Route::get('/laporan', [BendaharaController::class, 'laporan'])->name('laporan');

            Route::delete('/transaction/{transaction}', [BendaharaController::class, 'destroy'])->name('transaction.destroy');
        });
});
