<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; // Wajib: Pastikan model Category sudah ada dan di-import

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definisikan data kategori yang ingin dimasukkan
        $categories = [
            'Meja Kantor',
            'Kursi Ergonomis',
            'Lemari Arsip',
            'Pencahayaan',
            'Aksesoris Ruangan',
        ];

        // 2. Loop melalui array dan masukkan data ke database
        foreach ($categories as $categoryName) {
            // firstOrCreate: Mencari berdasarkan 'name'. Jika tidak ada, ia akan membuat record baru.
            Category::firstOrCreate(['name' => $categoryName]);
        }
    }
}