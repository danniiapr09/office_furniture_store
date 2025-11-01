<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FurnitureController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Endpoint khusus admin
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/furniture', [FurnitureController::class, 'store']);
    Route::put('/furniture/{id}', [FurnitureController::class, 'update']);
    Route::delete('/furniture/{id}', [FurnitureController::class, 'destroy']);
});

// Endpoint khusus user
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user-profile', [AuthController::class, 'profile']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', fn(Request $request) => $request->user());
    Route::put('/user/update', function (Request $request) {
        $user = $request->user();
        $user->update($request->only('name', 'phone'));
        return response()->json($user);
    });

    // ✅ Ganti ke AuthController atau hapus
    Route::post('/user/upload-photo', [AuthController::class, 'uploadPhoto']);

    Route::post('/furniture', [FurnitureController::class, 'store']);
    Route::put('/furniture/{furniture}', [FurnitureController::class, 'update']);
    Route::delete('/furniture/{furniture}', [FurnitureController::class, 'destroy']);

    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});