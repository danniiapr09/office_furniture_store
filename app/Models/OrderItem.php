<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Pastikan SEMUA kolom yang digunakan di OrderController@store ada di sini
    protected $fillable = [
        'order_id', 
        'furniture_id', // DIUBAH: merujuk ke tabel furnitures
        'quantity', 
        'price_per_unit',
    ];

    /**
     * Relasi: OrderItem dimiliki oleh satu Order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Relasi: OrderItem terhubung ke satu Furniture.
     */
    public function furniture()
    {
        // DIUBAH: merujuk ke Furniture::class
        return $this->belongsTo(Furniture::class);
    }
}