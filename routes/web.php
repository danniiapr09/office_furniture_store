<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FurnitureController; 
use App\Http\Controllers\Admin\OrderController as AdminOrderController; // <-- BARU DITAMBAH: Import Order Controller

// Halaman Login Admin
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

// Semua route di bawah ini hanya bisa diakses admin yang sudah login
Route::middleware(['admin'])->group(function () {

    // Dashboard Admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // =====================================================
    // Furniture Management (SUDAH DIPERBAIKI)
    // =====================================================
    Route::prefix('/admin/furnitures')->name('admin.furnitures.')->group(function () { 
        
        // Halaman Index (Hanya mengembalikan view, seperti yang Anda inginkan)
        Route::get('/', function () {
            return view('admin.furniture.index');
        })->name('index'); // admin.furnitures.index

        // ENDPOINT AJAX UNTUK MENGAMBIL DATA LIST (KRITIS)
        Route::get('/list', [FurnitureController::class, 'index'])->name('list'); // admin.furnitures.list
    });
    // Jika Anda ingin CRUD lengkap (Add/Edit/Delete), gunakan Route::resource!
    
    // =====================================================
    // Order Management (PESANAN) <-- BARU DITAMBAHKAN
    // =====================================================
    // Rute Pesanan untuk Admin Panel (index: Daftar, show: Detail)
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);

    // User Management
    Route::prefix('/admin/users')->name('admin.users.')->group(function () {

        // Halaman user
        Route::get('/', function () {
            return view('admin.users.index');
        })->name('index');

        // Ajax list user
        Route::get('/list', [UserController::class, 'index'])->name('list');

        // Detail user
        Route::get('/{id}', [UserController::class, 'show'])->name('show');

        // Update user
        Route::post('/{id}', [UserController::class, 'update'])->name('update');

        // Delete user
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Logout Admin
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});


// =================================================================
// ============== WORKAROUND FIX FOR RAILWAY (404 Error) ===========
// =================================================================

// Mencegat permintaan ke /storage/ (yang gagal karena storage:link) dan melayani file secara manual.
Route::get('/storage/{filename}', function ($filename) {
    // 1. Cek di dalam folder root public storage (misalnya 'YxXVfyIfWE.jpg')
    $path = storage_path('app/public/' . $filename);

    if (file_exists($path)) {
        return response()->file($path);
    }
    
    // 2. Cek di dalam sub-folder furniture (misalnya 'furniture/YxXVfyIfWE.jpg')
    // Asumsi path database Anda menyimpan 'furniture/namafile.jpg'
    if (strpos($filename, 'furniture/') === 0) {
        $subPath = storage_path('app/public/' . $filename);
        if (file_exists($subPath)) {
            return response()->file($subPath);
        }
    }
    
    // Jika file tidak ditemukan, kembalikan 404
    abort(404);

})->where('filename', '.*'); // Memastikan URL bisa menerima '/' seperti 'furniture/nama.jpg'

// Route /linkstorage Anda dipertahankan, namun Route manual di atas lebih efektif.
Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
    return 'Storage linked!';
});