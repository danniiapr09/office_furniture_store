<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Furniture extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk menunjuk ke tabel 'furniture' (tunggal)
    protected $table = 'furniture'; 
    
    // Kolom disesuaikan agar konsisten dengan Controller dan Migration yang diperbaiki.
    protected $fillable = [
        'nama',
        'category_id', 
        'harga',
        'stok',
        'deskripsi',
        'image'
    ];

    /**
     * Relasi many-to-one ke Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}