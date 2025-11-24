<?php

use App\Http\Controllers\Admin\AdminAuthController;

Route::prefix('admin')->group(function () {

    // Login form
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
        ->name('admin.login');

    // Process login
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->name('admin.login.submit');

    // Logout
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->name('admin.logout');

});
