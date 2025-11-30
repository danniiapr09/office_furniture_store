<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // FK ke tabel orders
            $table->foreignId('furniture_id')->constrained()->onDelete('cascade'); // FK ke tabel products
            $table->integer('quantity');
            $table->decimal('price_per_unit', 10, 2); // Harga produk saat pesanan dibuat (Penting!)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};