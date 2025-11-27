<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\AdminSeeder; // <-- PASTIKAN INI DI-IMPORT

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ----------------------------------------------------
        // Panggil SEMUA seeder yang dibutuhkan aplikasi.
        // ----------------------------------------------------
        $this->call([
            CategorySeeder::class, // <-- WAJIB: Untuk mengisi data dropdown
            AdminSeeder::class,    // <-- WAJIB: Untuk membuat user Admin
            // Tambahkan seeder lain di sini (misal: FurnitureSeeder, OtherUserSeeder, etc.)
        ]);
        
        // Catatan: Kami telah memindahkan logika pembuatan User::factory()->create()
        // ke dalam AdminSeeder.php agar file ini tetap rapi dan fokus pada panggilan.
    }
}