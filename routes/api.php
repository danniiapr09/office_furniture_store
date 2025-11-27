<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FurnitureController;

// ----------------------------------------------------------------------------------
// 1. PUBLIC AUTH ROUTES (Login/Register untuk Mobile/SPA User)
// ----------------------------------------------------------------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// ----------------------------------------------------------------------------------
// 2. ADMIN CRUD & PUBLIC READ API
//    - Rute ini digunakan oleh JAVASCRIPT di Admin Panel (index.blade.php) 
//      DAN oleh user umum (mobile/public store) untuk membaca data.
//    - Keamanan untuk operasi tulis (POST/PUT/DELETE) DIJAMIN oleh CSRF Token,
//      karena Admin sudah login via sesi web.
// ----------------------------------------------------------------------------------

// Furniture API:
// GET /api/furniture & GET /api/furniture/{id} (Public Read & Admin Read)
// POST /api/furniture (Admin Write/Store)
// PUT/PATCH /api/furniture/{id} (Admin Write/Update)
// DELETE /api/furniture/{id} (Admin Write/Delete)
Route::apiResource('furniture', FurnitureController::class);

// Categories API:
// GET /api/categories (Admin Dropdown & Public Read)
// Tambahkan CRUD Penuh jika Admin perlu mengelola kategori dari halaman lain:
Route::apiResource('categories', CategoryController::class)->except(['show']);
Route::get('categories/{id}', [CategoryController::class, 'show']); // Perlu show kategori


// ----------------------------------------------------------------------------------
// 3. USER API (Mobile/SPA Protected by Sanctum Token)
//    - Hanya user yang login (dengan token) yang bisa mengakses.
// ----------------------------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // Profile
    Route::get('/user-profile', [AuthController::class, 'profile']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user/upload-photo', [AuthController::class, 'uploadPhoto']);

    // Rute Tambahan yang hanya bisa diakses user (Contoh: Cart, Checkout)
    // ...

    // Ping test
    Route::get('/ping', function () {
        return response()->json(['message' => 'API connected ✅']);
    });
});