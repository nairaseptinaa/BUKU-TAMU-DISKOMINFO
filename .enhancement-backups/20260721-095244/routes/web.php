<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestbookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ServiceTypeController;
use App\Http\Controllers\Admin\StatisticController;

// ===== BUKU TAMU (Publik) =====
Route::get('/', [GuestbookController::class, 'create'])->name('guestbook.create');
Route::post('/buku-tamu', [GuestbookController::class, 'store'])->name('guestbook.store');

// ===== AUTH ADMIN =====
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// ===== DASHBOARD ADMIN (Wajib Login) =====
Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/update-skm', [AdminController::class, 'updateSkm'])->name('admin.update-skm');
    Route::get('/admin/pengaturan', [AdminController::class, 'settings'])->name('admin.settings');
    Route::get('/admin/statistik', [StatisticController::class, 'index'])->name('admin.statistics');
    Route::get('/admin/statistik/cetak', [StatisticController::class, 'printReport'])->name('admin.statistics.print');
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('departments', DepartmentController::class);
    });
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('departmens', DepartmentController::class);
        Route::resource('service-types', ServiceTypeController::class);
    });
});