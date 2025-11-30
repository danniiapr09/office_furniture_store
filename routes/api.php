<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FurnitureController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;


// ----------------------------------------------------------------------------------
// 1. PUBLIC AUTH ROUTES (Login/Register untuk Mobile/SPA User)
// ----------------------------------------------------------------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// ----------------------------------------------------------------------------------
// 2. ADMIN CRUD & PUBLIC READ API
// ----------------------------------------------------------------------------------
// Furniture API:
Route::apiResource('furnitures', FurnitureController::class);

// Categories API:
Route::apiResource('categories', CategoryController::class)->except(['show']);
Route::get('categories/{id}', [CategoryController::class, 'show']); 


// ----------------------------------------------------------------------------------
// 3. USER API (Mobile/SPA Protected by Sanctum Token)
// ----------------------------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // Profile
    Route::get('/user-profile', [AuthController::class, 'profile']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user/upload-photo', [AuthController::class, 'uploadPhoto']);

    // Rute Pesanan (Cart & Checkout)
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/payments/initiate', [PaymentController::class, 'initiate']); 
    
    // Rute History Pesanan & Detail Pesanan <-- BARU DITAMBAH
    Route::apiResource('orders', OrderController::class)->only(['index', 'show']);

    // Ping test
    Route::get('/ping', function () {
        return response()->json(['message' => 'API connected ✅']);
    });
});

// ----------------------------------------------------------------------------------
// 4. PAYMENT GATEWAY WEBHOOK (Public Access for Midtrans/Xendit to send status update)
// ----------------------------------------------------------------------------------

// Route ini harus diakses PUBLIK (di luar auth:sanctum)
Route::post('/payments/webhook', [PaymentController::class, 'handleWebhook']);