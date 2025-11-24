<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;

// Halaman Login Admin
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

// Hanya admin yang sudah login
Route::middleware(['web', 'admin'])->group(function () {

    // Dashboard Admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); // nanti kita buat blade-nya
    })->name('admin.dashboard');

    // Logout Admin
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

});
