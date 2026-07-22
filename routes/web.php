<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ServiceTypeController;
use App\Http\Controllers\Admin\StatisticController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuestbookController;
use Illuminate\Support\Facades\Route;

// ===== BUKU TAMU (Publik) =====
Route::get('/', [GuestbookController::class, 'create'])->name('guestbook.create');
Route::post('/buku-tamu', [GuestbookController::class, 'store'])->name('guestbook.store');

// ===== AUTH ADMIN =====
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// ===== DASHBOARD ADMIN (Wajib Login) =====
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/tamu/{guest}/edit', [AdminController::class, 'editGuest'])->name('admin.guests.edit');
    Route::put('/admin/tamu/{guest}', [AdminController::class, 'updateGuest'])->name('admin.guests.update');
    Route::delete('/admin/tamu/{guest}', [AdminController::class, 'destroyGuest'])->name('admin.guests.destroy');
    Route::post('/update-skm', [AdminController::class, 'updateSkm'])->name('update-skm');
    Route::get('/pengaturan', [AdminController::class, 'settings'])->name('settings');

    Route::get('/statistik', [StatisticController::class, 'index'])->name('statistics');
    Route::get('/statistik/cetak', [StatisticController::class, 'printReport'])->name('statistics.print');

    Route::resource('departments', DepartmentController::class)->except(['show']);
    Route::resource('service-types', ServiceTypeController::class)->except(['show']);
});
