<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    // Pastikan SEMUA kolom yang digunakan di OrderController@store didaftarkan di sini
    protected $fillable = [
        'user_id', 
        'total_amount', 
        'status', 
        'shipping_address',
        // Tambahkan kolom lain jika ada (misalnya: shipping_cost, payment_method)
    ];

    /**
     * Relasi: Sebuah Order dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Sebuah Order memiliki banyak OrderItem.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * Relasi: Sebuah Order memiliki banyak Payment (untuk riwayat pembayaran).
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}