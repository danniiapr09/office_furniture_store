<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\UserController;

// Halaman Login Admin
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

// Hanya admin yang sudah login
Route::middleware(['web', 'admin'])->group(function () {

    // Dashboard Admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); // nanti kita buat blade-nya
    })->name('admin.dashboard');
    // Tabel furniture
    Route::get('/admin/furniture', function () {
        return view('admin.furniture.index');
    })->name('admin.furniture');
    // tabel users
    Route::get('/admin/users', function () {
        return view('admin.users.index');
    })->name('admin.users');
    Route::get('/admin/users/list', [UserController::class,'index']); // ajax data
    Route::get('/admin/users/{id}', [UserController::class,'show']); // detail
    Route::post('/admin/users/{id}', [UserController::class,'update']); // _method=PUT
    Route::delete('/admin/users/{id}', [UserController::class,'destroy']);

    // Logout Admin
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

});
