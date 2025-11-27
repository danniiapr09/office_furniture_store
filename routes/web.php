<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;

// Halaman Login Admin
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

// Semua route di bawah ini hanya bisa diakses admin yang sudah login
Route::middleware(['admin'])->group(function () {

    // Dashboard Admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Furniture Management
    Route::get('/admin/furniture', function () {
        return view('admin.furniture.index');
    })->name('admin.furniture.index');

    // User Management
    Route::prefix('/admin/users')->name('admin.users.')->group(function () {

        // Menampilkan halaman user
        Route::get('/', function () {
            return view('admin.users.index');
        })->name('index');

        // Ajax list user
        Route::get('/list', [UserController::class, 'index'])->name('list');

        // Detail user
        Route::get('/{id}', [UserController::class, 'show'])->name('show');

        // Update user
        Route::post('/{id}', [UserController::class, 'update'])->name('update');

        // Delete user
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Logout Admin
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});
