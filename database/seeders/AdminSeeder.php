<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus (delete) jika user sudah ada agar tidak duplikat
        User::where('email', 'admin@example.com')->delete();

        // Membuat User Admin (sama seperti yang Anda buat sebelumnya)
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // Password: 'password'
            // Jika Anda punya kolom 'is_admin', tambahkan: 'is_admin' => true,
        ]);
    }
}