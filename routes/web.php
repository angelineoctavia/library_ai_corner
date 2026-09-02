<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AiToolController;

// --- Route Mahasiswa & Public ---
Route::get('/', [AIDashboardController::class, 'index'])->name('dashboard');
Route::get('/student/login', [AIDashboardController::class, 'showLogin'])->name('student.login.page');
Route::post('/student/login', [AIDashboardController::class, 'processLogin'])->name('student.login.submit');
Route::post('/student/logout', [AIDashboardController::class, 'logout'])->name('student.logout');
Route::get('/ai/visit/{id}', [AIDashboardController::class, 'trackUsage'])->name('ai.visit');

// --- Route Admin ---
Route::get('/admin/login', [AdminController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'processAdminLogin'])->name('admin.login.process');
Route::get('/admin/control-panel', [AdminController::class, 'controlPanel'])->name('admin.control.panel');
Route::post('/admin/ai/store', [AdminController::class, 'store'])->name('admin.ai.store');
Route::put('/admin/ai/update/{id}', [AdminController::class, 'update'])->name('admin.ai.update');
Route::delete('/admin/ai/delete/{id}', [AdminController::class, 'destroy'])->name('admin.ai.delete');
Route::put('/admin/ai/restore/{id}', [AdminController::class, 'restore'])->name('admin.ai.restore');
Route::get('/admin/logout', [AdminController::class, 'adminLogout'])->name('admin.logout');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/dashboard/export', [AdminController::class, 'exportExcel'])->name('admin.dashboard.export');


// Rute utama & CRUD
Route::get('/control-panel', [AiToolController::class, 'controlPanel'])->name('control.panel');

// Rute tambahan untuk Inactive & Restore
Route::get('/control-panel/inactive', [AiToolController::class, 'inactive'])->name('control.panel.inactive');
Route::put('/control-panel/{id}/restore', [AiToolController::class, 'restore'])->name('ai-tools.restore');

// Pastikan route delete/destroy kamu mengubah status_del jadi '1' (bukan delete permanen)
Route::delete('/ai-tools/{id}', [AiToolController::class, 'destroy'])->name('ai-tools.destroy');

Route::get('/ai/visit/{id}', [AIDashboardController::class, 'trackUsage'])->name('ai.visit');
Route::post('/ai/close-session', [AIDashboardController::class, 'closeSession'])->name('ai.close-session');