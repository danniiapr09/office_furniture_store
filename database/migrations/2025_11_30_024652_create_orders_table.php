<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // FK ke tabel users
            $table->decimal('total_amount', 10, 2); // Total biaya pesanan
            $table->enum('status', ['Pending', 'Processing', 'Paid', 'Shipped', 'Delivered', 'Cancelled'])->default('Pending');
            $table->text('shipping_address');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};