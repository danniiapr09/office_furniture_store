<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique(); // ID Transaksi Midtrans
            $table->decimal('gross_amount', 10, 2);
            $table->string('payment_type');
            $table->enum('status', ['pending', 'settlement', 'expire', 'cancel']);
            $table->json('raw_response'); // Simpan semua data notifikasi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};