<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Furniture extends Model
{
    use HasFactory;

    // Kolom disesuaikan agar konsisten dengan Controller dan Migration yang diperbaiki.
    protected $fillable = [
        'nama',
        'category_id', // Gunakan category_id untuk relasi foreign key
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
        // Mengasumsikan foreign key-nya adalah 'category_id'
        return $this->belongsTo(Category::class, 'category_id');
    }
}