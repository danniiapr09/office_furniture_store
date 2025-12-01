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
        Schema::table('orders', function (Blueprint $table) {
            // Nambah kolom BARU di tabel orders yang SUDAH ADA

            // metode pengiriman, misal: 'JNE', 'J&T', 'COD', dll
            $table->string('shipping_method')
                  ->nullable()
                  ->after('shipping_cost');

            // metode pembayaran, misal: 'midtrans', 'bank_transfer', 'cod', dll
            $table->string('payment_method')
                  ->nullable()
                  ->after('shipping_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Kalau di-rollback, kolom barunya dihapus
            $table->dropColumn(['shipping_method', 'payment_method']);
        });
    }
};