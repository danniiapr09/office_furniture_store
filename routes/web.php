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
    // // tabel users
    Route::prefix('/admin/users')->name('admin.users.')->group(function () {
        
        // Rute: /admin/users (menampilkan halaman)
        Route::get('/', function () {
            return view('admin.users.index');
        })->name('index'); // nama rute: admin.users.index
        
        // Rute: /admin/users/list (ajax data)
        Route::get('/list', [UserController::class, 'index']);
        
        // Rute: /admin/users/{id} (detail)
        Route::get('/{id}', [UserController::class, 'show']); 
        
        // Rute: POST /admin/users/{id} (untuk Update dengan _method=PUT)
        Route::post('/{id}', [UserController::class, 'update']);
        
        // Rute: DELETE /admin/users/{id} (Delete)
        Route::delete('/{id}', [UserController::class, 'destroy']);
        
    });

    // Logout Admin
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

});
