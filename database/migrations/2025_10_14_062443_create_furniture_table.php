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
        // Pastikan tabel categories sudah dibuat SEBELUM tabel furnitures
        Schema::create('furnitures', function (Blueprint $table) {
            $table->id();
            
            // Kolom Data
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('category_id'); // Foreign key ke tabel categories
            $table->integer('harga');
            $table->integer('stok')->default(0);
            $table->string('image')->nullable(); // Path gambar

            // Timestamps
            $table->timestamps();

            // Definisi Foreign Key
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('furnitures');
    }
};