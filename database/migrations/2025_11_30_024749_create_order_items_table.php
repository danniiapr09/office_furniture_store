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

            // FK ke tabel orders
            $table->foreignId('order_id')
                  ->constrained()
                  ->onDelete('cascade');

            // FIX: sesuaikan dengan nama tabel 'furniture'
            $table->foreignId('furniture_id')
                  ->constrained('furniture')
                  ->onDelete('cascade');

            $table->integer('quantity');
            $table->decimal('price_per_unit', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
