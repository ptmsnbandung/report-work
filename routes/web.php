<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumentasiController;
use App\Http\Controllers\GeocodeController;
use App\Http\Controllers\KronologisController;
use App\Http\Controllers\ManuverCoreController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\TitikPerbaikanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Aplikasi Tiketing Gangguan PT Connecti Jelajah Priangan
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware(['auth', 'role'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Modul Tiket
    Route::get('/tiket', [TiketController::class, 'index'])->name('tiket.index');

    // Helpdesk & Admin Tiket Operations
    Route::middleware('role:admin,helpdesk')->group(function () {
        Route::get('/tiket/create', [TiketController::class, 'create'])->name('tiket.create');
        Route::post('/tiket', [TiketController::class, 'store'])->name('tiket.store');
        Route::get('/tiket/preview-number', [TiketController::class, 'previewNumber'])->name('tiket.preview-number');
        Route::get('/tiket/{tiket}/edit', [TiketController::class, 'edit'])->name('tiket.edit');
        Route::put('/tiket/{tiket}', [TiketController::class, 'update'])->name('tiket.update');
        Route::post('/tiket/{tiket}/close', [TiketController::class, 'close'])->name('tiket.close');
        Route::post('/tiket/{tiket}/reject-closing-awal', [TiketController::class, 'rejectClosingAwal'])->name('tiket.reject-closing-awal');
    });

    // Teknisi & Admin: Closing Awal Tiket
    Route::post('/tiket/{tiket}/closing-awal', [TiketController::class, 'closingAwal'])
        ->middleware('role:admin,teknis')
        ->name('tiket.closing-awal');

    // Stop Clock & Handover Shift Operations
    Route::middleware('role:admin,helpdesk,teknis')->group(function () {
        Route::post('/tiket/{tiket}/stop-clock/start', [TiketController::class, 'startStopClock'])->name('tiket.stop-clock.start');
        Route::post('/tiket/{tiket}/stop-clock/stop', [TiketController::class, 'stopStopClock'])->name('tiket.stop-clock.stop');
        Route::post('/tiket/{tiket}/handover-shift', [TiketController::class, 'handoverShift'])->name('tiket.handover-shift');
        Route::get('/tiket/{tiket}/prerequisites', [TiketController::class, 'checkPrerequisites'])->name('tiket.prerequisites');
    });

    // Detail Tiket (Bisa diakses semua role yang login)
    Route::get('/tiket/{tiket}', [TiketController::class, 'show'])->name('tiket.show');

    // Hapus Tiket (Admin only)
    Route::delete('/tiket/{tiket}', [TiketController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('tiket.destroy');

    // Modul Kronologis (Fase 3)
    Route::get('/tiket/{tiket}/kronologis', [KronologisController::class, 'index'])->name('tiket.kronologis.index');
    Route::post('/tiket/{tiket}/kronologis', [KronologisController::class, 'store'])
        ->middleware('role:admin,helpdesk,teknis')
        ->name('tiket.kronologis.store');
    Route::put('/tiket/{tiket}/kronologis/{kronologis}', [KronologisController::class, 'update'])
        ->middleware('role:admin,helpdesk,teknis')
        ->name('tiket.kronologis.update');
    Route::delete('/tiket/{tiket}/kronologis/{kronologis}', [KronologisController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('tiket.kronologis.destroy');

    // Modul Resume & Material (Fase 4)
    Route::post('/tiket/{tiket}/resume', [ResumeController::class, 'store'])
        ->middleware('role:admin,helpdesk,teknis')
        ->name('tiket.resume.store');
    Route::put('/tiket/{tiket}/resume', [ResumeController::class, 'update'])
        ->middleware('role:admin,helpdesk,teknis')
        ->name('tiket.resume.update');

    Route::post('/tiket/{tiket}/material', [MaterialController::class, 'store'])
        ->middleware('role:admin,helpdesk,teknis')
        ->name('tiket.material.store');
    Route::delete('/tiket/{tiket}/material/{material}', [MaterialController::class, 'destroy'])
        ->middleware('role:admin,helpdesk,teknis')
        ->name('tiket.material.destroy');

    Route::post('/tiket/{tiket}/titik-perbaikan', [TitikPerbaikanController::class, 'store'])
        ->middleware('role:admin,helpdesk,teknis')
        ->name('tiket.titik-perbaikan.store');
    Route::delete('/tiket/{tiket}/titik-perbaikan/{titikPerbaikan}', [TitikPerbaikanController::class, 'destroy'])
        ->middleware('role:admin,helpdesk,teknis')
        ->name('tiket.titik-perbaikan.destroy');

    // Modul Dokumentasi & Manuver Core (Fase 5)
    Route::post('/tiket/{tiket}/dokumentasi', [DokumentasiController::class, 'store'])
        ->middleware('role:admin,helpdesk,teknis')
        ->name('tiket.dokumentasi.store');
    Route::delete('/dokumentasi/{dokumentasi}', [DokumentasiController::class, 'destroy'])
        ->middleware('role:admin,helpdesk,teknis')
        ->name('tiket.dokumentasi.destroy');

    Route::post('/tiket/{tiket}/manuver-core', [ManuverCoreController::class, 'store'])
        ->middleware('role:admin,helpdesk,teknis')
        ->name('tiket.manuver-core.store');
    Route::delete('/manuver-core/{manuverCore}', [ManuverCoreController::class, 'destroy'])
        ->middleware('role:admin,helpdesk,teknis')
        ->name('tiket.manuver-core.destroy');

    // API Helper (Geocoding & Reverse Geocode)
    Route::get('/api/reverse-geocode', [GeocodeController::class, 'reverse'])->name('api.reverse-geocode');

    // Modul MTTR / SLA & Reporting (Fase 6)
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/kpi', [\App\Http\Controllers\ReportController::class, 'kpi'])->name('reports.kpi');
    Route::get('/reports/shifts', [\App\Http\Controllers\ReportController::class, 'shifts'])->name('reports.shifts');
    Route::get('/reports/mttr-data', [\App\Http\Controllers\ReportController::class, 'mttr'])->name('reports.mttr');
    Route::get('/reports/sla-data', [\App\Http\Controllers\ReportController::class, 'sla'])->name('reports.sla');
    Route::get('/reports/export/pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/excel', [\App\Http\Controllers\ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export-shifts/pdf', [\App\Http\Controllers\ReportController::class, 'exportShiftsPdf'])->name('reports.export.shifts.pdf');
    Route::get('/reports/export-shifts/excel', [\App\Http\Controllers\ReportController::class, 'exportShiftsExcel'])->name('reports.export.shifts.excel');
    Route::get('/tiket/{tiket}/export/pdf', [\App\Http\Controllers\ReportController::class, 'exportTiketPdf'])->name('reports.export.tiket.pdf');

    // Modul Notifikasi (Fase 7)
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-json', [\App\Http\Controllers\NotificationController::class, 'unreadJson'])->name('notifications.unread_json');
    Route::match(['get', 'post'], '/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::match(['get', 'post'], '/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Modul Master Data - Admin Only (Fase 7)
    Route::middleware('role:admin')->prefix('master')->name('master.')->group(function () {
        // Master Segment
        Route::get('/segments', [\App\Http\Controllers\Master\MasterSegmentController::class, 'index'])->name('segments.index');
        Route::post('/segments', [\App\Http\Controllers\Master\MasterSegmentController::class, 'store'])->name('segments.store');
        Route::put('/segments/{segment}', [\App\Http\Controllers\Master\MasterSegmentController::class, 'update'])->name('segments.update');
        Route::delete('/segments/{segment}', [\App\Http\Controllers\Master\MasterSegmentController::class, 'destroy'])->name('segments.destroy');

        // Master Material
        Route::get('/materials', [\App\Http\Controllers\Master\MasterMaterialController::class, 'index'])->name('materials.index');
        Route::post('/materials', [\App\Http\Controllers\Master\MasterMaterialController::class, 'store'])->name('materials.store');
        Route::put('/materials/{material}', [\App\Http\Controllers\Master\MasterMaterialController::class, 'update'])->name('materials.update');
        Route::delete('/materials/{material}', [\App\Http\Controllers\Master\MasterMaterialController::class, 'destroy'])->name('materials.destroy');

        // Master SLA
        Route::get('/sla', [\App\Http\Controllers\Master\MasterSlaController::class, 'index'])->name('sla.index');
        Route::post('/sla', [\App\Http\Controllers\Master\MasterSlaController::class, 'store'])->name('sla.store');
        Route::put('/sla/{sla}', [\App\Http\Controllers\Master\MasterSlaController::class, 'update'])->name('sla.update');
        Route::delete('/sla/{sla}', [\App\Http\Controllers\Master\MasterSlaController::class, 'destroy'])->name('sla.destroy');

        // Master User & Role
        Route::get('/users', [\App\Http\Controllers\Master\MasterUserController::class, 'index'])->name('users.index');
        Route::post('/users', [\App\Http\Controllers\Master\MasterUserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [\App\Http\Controllers\Master\MasterUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Master\MasterUserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/toggle-active', [\App\Http\Controllers\Master\MasterUserController::class, 'toggleActive'])->name('users.toggle_active');
        Route::post('/users/{user}/reset-password', [\App\Http\Controllers\Master\MasterUserController::class, 'resetPassword'])->name('users.reset_password');
    });

    // Web Push Notification Device Subscription Routes
    Route::get('/push/vapid-public-key', [\App\Http\Controllers\PushSubscriptionController::class, 'vapidPublicKey'])->name('push.vapid');
    Route::post('/push/subscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
    Route::post('/push/test', [\App\Http\Controllers\PushSubscriptionController::class, 'testPush'])->name('push.test');
});



