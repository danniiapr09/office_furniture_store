<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\UserController;
// Tambahkan Controller yang diperlukan untuk Furniture dan Category
use App\Http\Controllers\Admin\FurnitureController; 
use App\Http\Controllers\Admin\CategoryController; 

// Halaman Login Admin
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

// Semua route di bawah ini hanya bisa diakses admin yang sudah login
Route::middleware(['web', 'admin'])->group(function () {

    // Dashboard Admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Furniture Management (VIEW)
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

        // Update user (_method=PUT via POST)
        Route::post('/{id}', [UserController::class, 'update'])->name('update');

        // Delete user
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // ===============================================
    // --- RUTE API (AJAX FETCH) ---
    // KRITIS: Dilindungi oleh middleware 'auth:sanctum'
    // ===============================================
    Route::prefix('api')->middleware('auth:sanctum')->group(function () {
        
        // Categories API (Read Only)
        Route::get('categories', [CategoryController::class, 'index']);
        
        // Furniture CRUD API
        Route::get('furniture', [FurnitureController::class, 'index']);       // LIST & SEARCH
        Route::post('furniture', [FurnitureController::class, 'store']);     // CREATE
        Route::get('furniture/{furniture}', [FurnitureController::class, 'show']); // DETAIL
        Route::post('furniture/{furniture}', [FurnitureController::class, 'update']); // UPDATE (via POST dengan _method=PUT)
        Route::delete('furniture/{furniture}', [FurnitureController::class, 'destroy']); // DELETE

    });


    // Logout Admin
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});