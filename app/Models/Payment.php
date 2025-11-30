<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id', 
        'transaction_id', 
        'gross_amount', 
        'payment_type',
        'status',
        'raw_response' // Untuk menyimpan data notifikasi mentah dari Payment Gateway
    ];

    /**
     * Relasi: Pembayaran terhubung ke satu Order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}