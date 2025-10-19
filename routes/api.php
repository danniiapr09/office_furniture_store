<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FurnitureController;

// ====================
// RUTE AUTH (PUBLIC)
// ====================

// Register
Route::post('/register', [AuthController::class, 'register']);

// Login 
Route::post('/login', [AuthController::class, 'login']);

// ====================
// RUTE PROFILE (BUTUH LOGIN)
// ====================
Route::middleware('auth:sanctum')->group(function () {
    
    // Ambil profile
    Route::get('/user-profile', [AuthController::class, 'profile']);

    // Update profile
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cek user info (opsional)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/user/update', function (Request $request) {
        $user = $request->user();
        $user->update($request->only('name', 'phone'));
        return response()->json($user);
    });
    Route::post('/user/upload-photo', [UserController::class, 'uploadPhoto']);


    // ====================
    // FURNITURE
    // ====================
    Route::get('/furnitures', [FurnitureController::class, 'index']);
    Route::get('/furnitures/category/{id}', [FurnitureController::class, 'byCategory']);
    Route::get('/furniture/search', [FurnitureController::class, 'search']);
    Route::post('/furniture', [FurnitureController::class, 'store']);
    Route::put('/furniture/{furniture}', [FurnitureController::class, 'update']);
    Route::delete('/furniture/{furniture}', [FurnitureController::class, 'destroy']);

    // ====================
    // CATEGORY
    // ====================
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});