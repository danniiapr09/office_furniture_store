<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FurnitureController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua rute API didefinisikan di sini. 
| Dibagi menjadi dua kelompok: Public (tanpa login) dan Protected (dengan auth:sanctum).
|
*/

// ======================================================================
// 🟢 PUBLIC ROUTES (BISA DIAKSES TANPA LOGIN)
// ======================================================================

// AUTH
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// FURNITURE
Route::get('/furnitures', [FurnitureController::class, 'index']);
Route::get('/furnitures/category/{id}', [FurnitureController::class, 'byCategory']);

// PENCARIAN
Route::get('/furniture/search', [FurnitureController::class, 'search']); // full filter (kategori, harga, dll)
Route::get('/furniture/simple-search', [FurnitureController::class, 'simpleSearch']); // pakai ?q= query

// CATEGORY
Route::get('/categories', [CategoryController::class, 'index']);


// ======================================================================
// 🔒 PROTECTED ROUTES (BUTUH LOGIN - MENGGUNAKAN sanctum)
// ======================================================================
Route::middleware('auth:sanctum')->group(function () {

    // ==========================================================
    // 👤 USER PROFILE
    // ==========================================================
    Route::get('/user-profile', [AuthController::class, 'profile']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Informasi user (opsional)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Update data user
    Route::put('/user/update', function (Request $request) {
        $user = $request->user();
        $user->update($request->only('name', 'phone'));
        return response()->json($user);
    });

    // Upload foto profil user
    Route::post('/user/upload-photo', [UserController::class, 'uploadPhoto']);


    // ==========================================================
    // 🛋️ FURNITURE MANAGEMENT (CRUD)
    // ==========================================================
    Route::post('/furniture', [FurnitureController::class, 'store']);
    Route::put('/furniture/{furniture}', [FurnitureController::class, 'update']);
    Route::delete('/furniture/{furniture}', [FurnitureController::class, 'destroy']);


    // ==========================================================
    // 🏷️ CATEGORY MANAGEMENT (CRUD)
    // ==========================================================
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});
