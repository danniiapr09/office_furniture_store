<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FurnitureController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// USER API (mobile)
Route::middleware('auth:sanctum')->group(function () {

    // Profile
    Route::get('/user-profile', [AuthController::class, 'profile']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user/upload-photo', [AuthController::class, 'uploadPhoto']);

    // Furniture (READ ONLY)
    Route::get('/furniture', [FurnitureController::class,'index']);
    Route::get('/furniture/{id}', [FurnitureController::class,'show']);
    Route::post('/furniture', [FurnitureController::class,'store']);
    Route::post('/furniture/{id}', [FurnitureController::class,'update']);
    Route::delete('/furniture/{id}', [FurnitureController::class,'destroy']);

    // Categories (READ ONLY)
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    // Ping test
    Route::get('/ping', function () {
        return response()->json(['message' => 'API connected ✅']);
    });
});
