<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // HANYA MEMBUAT TABEL ORDERS
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // FK ke tabel users
                $table->decimal('total_amount', 10, 2); // Total biaya pesanan

                // Menggunakan ENUM (seperti usulan Anda) untuk status yang lebih terstruktur
                $table->enum('status', ['Pending', 'Processing', 'Paid', 'Shipped', 'Delivered', 'Cancelled'])->default('Pending');
                
                $table->text('shipping_address');
                
                // --- KOLOM PENTING UNTUK CHECKOUT ---
                // Kolom yang menyebabkan error 500 karena tidak ada / NULL
                $table->string('contact_phone', 20)->nullable(); // No. Kontak, diisi oleh user saat checkout
                $table->decimal('shipping_cost', 8, 2)->default(0.0); // Biaya Kirim
                // --- END KOLOM PENTING UNTUK CHECKOUT ---

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};