<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\CategorySeeder; // Import CategorySeeder

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ----------------------------------------------------
        // 1. Panggil Seeder untuk data relasi (Category)
        // ----------------------------------------------------
        $this->call([
            CategorySeeder::class, // <-- TAMBAHKAN INI
        ]);


        // ----------------------------------------------------
        // 2. Buat Data User (Admin atau Test User)
        // ----------------------------------------------------
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User', // Ganti nama untuk identifikasi mudah
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // Tambahkan password agar bisa login
        ]);
        
        // Catatan: Pastikan Anda memiliki kolom 'is_admin' di tabel 'users' 
        // dan set nilainya menjadi true jika Anda menggunakan role-based authorization.
    }
}