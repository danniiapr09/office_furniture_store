<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name' // Gunakan 'name' di sini untuk kategori
    ];

    /**
     * Relasi one-to-many ke Furniture.
     */
    public function furniture()
    {
        return $this->hasMany(Furniture::class);
    }
}